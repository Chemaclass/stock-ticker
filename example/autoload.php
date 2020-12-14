<?php

declare(strict_types=1);

use Chemaclass\TickerNews\Domain\Crawler\CrawlResult;
use Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\BarronsSiteCrawler;
use Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlCrawler;
use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractor;
use Chemaclass\TickerNews\Domain\Crawler\Site\Shared\NewsNormalizer;
use Chemaclass\TickerNews\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\TickerNews\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\TickerNews\Domain\Notifier\Channel\Slack\SlackHttpClient;
use Chemaclass\TickerNews\Domain\Notifier\Channel\TwigTemplateGenerator;
use Chemaclass\TickerNews\Domain\Notifier\ChannelInterface;
use Chemaclass\TickerNews\Domain\Notifier\NotifierPolicy;
use Chemaclass\TickerNews\Domain\Notifier\NotifyResult;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\TickerNews\TickerNewsFacade;
use Chemaclass\TickerNews\TickerNewsFactory;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

/*
 * ======================================================
 * These classes has only static function intentionally.
 * They are basically grouping functions by their intention
 * and responsibility.
 *
 * This autoload.php and these classes are just for
 * the example usage.
 * ======================================================
 */

/**
 * The role of this class is to be the facade's example.
 *
 * A facade is an object that serves as a front-facing interface
 * masking more complex underlying or structural code.
 */
final class TickerNews
{
    /**
     * @param ChannelInterface[] $channels
     * @param array<string, PolicyGroup> $groupedPolicies
     */
    public static function sendNotifications(array $channels, array $groupedPolicies): NotifyResult
    {
        $policy = new NotifierPolicy($groupedPolicies);
        $tickerSymbols = array_keys($groupedPolicies);

        return Factory::createTickerNewsFacade(...$channels)
            ->notify($policy, self::crawlStock($tickerSymbols));
    }

    /**
     * @param string[] $tickerSymbols
     * @param int $maxNewsToFetch The max number of news that might be crawl.
     */
    public static function crawlStock(array $tickerSymbols, int $maxNewsToFetch = 2): CrawlResult
    {
        return Factory::createTickerNewsFacade()
            ->crawlStock(
                Factory::createAllSiteCrawlers($maxNewsToFetch),
                $tickerSymbols
            );
    }
}

/**
 * This one is where all creational function are.
 *
 * The factory method pattern is used to deal with the problem
 * of creating different "somehow related" objects.
 */
final class Factory
{
    public static function createTickerNewsFacade(ChannelInterface ...$channels): TickerNewsFacade
    {
        return new TickerNewsFacade(
            new TickerNewsFactory(
                HttpClient::create(),
                ...$channels
            )
        );
    }

    public static function createEmailChannel(string $templateName = 'email.twig'): EmailChannel
    {
        return new EmailChannel(
            $_ENV['TO_ADDRESS'],
            new Mailer(new GmailSmtpTransport(
                $_ENV['MAILER_USERNAME'],
                $_ENV['MAILER_PASSWORD']
            )),
            new TwigTemplateGenerator(
                new Environment(new FilesystemLoader(__DIR__ . '/templates')),
                $templateName
            )
        );
    }

    public static function createSlackChannel(string $templateName = 'slack.twig'): SlackChannel
    {
        return new SlackChannel(
            $_ENV['SLACK_DESTINY_CHANNEL_ID'],
            new SlackHttpClient(HttpClient::create([
                'auth_bearer' => $_ENV['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'],
            ])),
            new TwigTemplateGenerator(
                new Environment(new FilesystemLoader(__DIR__ . '/templates')),
                $templateName
            )
        );
    }

    public static function createAllSiteCrawlers(int $maxNewsToFetch): array
    {
        return [
            self::createFinanceYahooSiteCrawler($maxNewsToFetch),
            self::createBarronsSiteCrawler($maxNewsToFetch),
        ];
    }

    private static function createFinanceYahooSiteCrawler(int $maxNewsToFetch = 3): FinanceYahooSiteCrawler
    {
        return new FinanceYahooSiteCrawler([
            'name' => new JsonExtractor\QuoteSummaryStore\CompanyName(),
            'price' => new JsonExtractor\QuoteSummaryStore\RegularMarketPrice(),
            'change' => new JsonExtractor\QuoteSummaryStore\RegularMarketChange(),
            'changePercent' => new JsonExtractor\QuoteSummaryStore\RegularMarketChangePercent(),
            'trend' => new JsonExtractor\QuoteSummaryStore\RecommendationTrend(),
            'news' => new JsonExtractor\StreamStore\News(self::createNewsNormalizer($maxNewsToFetch)),
            'url' => new JsonExtractor\RouteStore\ExternalUrl(),
        ]);
    }

    private static function createBarronsSiteCrawler(int $maxNewsToFetch = 3): BarronsSiteCrawler
    {
        return new BarronsSiteCrawler([
            'news' => new HtmlCrawler\News(self::createNewsNormalizer($maxNewsToFetch)),
        ]);
    }

    private static function createNewsNormalizer(int $maxNewsToFetch = 3): NewsNormalizer
    {
        return new NewsNormalizer(new DateTimeZone('Europe/Berlin'), $maxNewsToFetch);
    }
}

/**
 * This is the place for all I/O functions.
 */
final class IO
{
    private const DEFAULT_SYMBOLS = ['AMZN', 'GOOG', 'TSLA'];

    public static function printCrawResult(CrawlResult $crawlResult): void
    {
        if ($crawlResult->isEmpty()) {
            self::println('Nothing new here...');

            return;
        }

        self::println('~~~~~~~~~~~~~~~~~~~~~~~~~~');
        self::println('~~~~~~ Crawl result ~~~~~~');
        self::println('~~~~~~~~~~~~~~~~~~~~~~~~~~');

        foreach ($crawlResult->getCompaniesGroupedBySymbol() as $symbol => $company) {
            self::println($symbol);

            foreach ($company->allInfo() as $key => $value) {
                self::printfln('# %s => %s', $key, json_encode($value));
            }

            self::println();
        }

        self::println();
    }

    /**
     * @psalm-suppress MissingParamType
     */
    public static function printfln(string $fmt = '', ...$args): void
    {
        self::println(sprintf($fmt, ...$args));
    }

    public static function printNotifyResult(NotifyResult $notifyResult): void
    {
        if ($notifyResult->isEmpty()) {
            self::println('Nothing new here...');

            return;
        }

        self::println('===========================');
        self::println('====== Notify result ======');
        self::println('===========================');

        foreach ($notifyResult->conditionNamesGroupBySymbol() as $symbol => $conditionNames) {
            self::println($symbol);
            self::println('Conditions:');

            foreach ($conditionNames as $conditionName) {
                self::printfln('  - %s', $conditionName);
            }

            self::println();
        }
        self::println();
    }

    public static function sleepWithPrompt(int $sec): void
    {
        self::println("Sleeping {$sec} seconds...");
        $len = mb_strlen((string)$sec);

        for ($i = $sec; $i > 0; $i--) {
            print sprintf("%0{$len}d\r", $i);
            sleep(1);
        }

        self::println('Awake again!');
    }

    public static function println(string $str = ''): void
    {
        print $str . PHP_EOL;
    }

    public static function readSymbolsFromInput(array $argv): array
    {
        return count($argv) <= 1
            ? self::DEFAULT_SYMBOLS
            : array_slice($argv, 1);
    }
}
