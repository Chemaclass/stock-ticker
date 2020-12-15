<?php

declare(strict_types=1);

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\BarronsSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\HtmlCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizer;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackHttpClient;
use Chemaclass\StockTicker\Domain\Notifier\Channel\TwigTemplateGenerator;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\FoundMoreNews;
use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\StockTicker\StockTickerFacade;
use Chemaclass\StockTicker\StockTickerFactory;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

/*
 * ======================================================
 * This `autoload.php` and these classes are just for
 * the example usage. They are basically a grouping of
 * functions by their intention and responsibility.
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
    private const DEFAULT_MAX_NEWS_TO_FETCH = 9;

    private Factory $factory;

    public static function create(): self
    {
        return new self(new Factory($_ENV));
    }

    private function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string[] $channelNames
     * @param array<string, PolicyGroup> $groupedPolicies
     */
    public function sendNotifications(
        array $channelNames,
        array $groupedPolicies,
        int $maxNewsToFetch = self::DEFAULT_MAX_NEWS_TO_FETCH
    ): NotifyResult {
        $policy = new NotifierPolicy($groupedPolicies);
        $symbols = array_keys($groupedPolicies);

        $channels = $this->factory->createChannelByNames($channelNames);

        return $this->factory
            ->createTickerNewsFacade(...$channels)
            ->notify($policy, $this->crawlStock($symbols, $maxNewsToFetch));
    }

    /**
     * @param string[] $symbols
     */
    public function crawlStock(
        array $symbols,
        int $maxNewsToFetch = self::DEFAULT_MAX_NEWS_TO_FETCH
    ): CrawlResult {
        return $this->factory
            ->createTickerNewsFacade()
            ->crawlStock(
                $this->factory->createAllSiteCrawlers($maxNewsToFetch),
                $symbols
            );
    }
}

/**
 * This class is where all creational function are.
 *
 * The factory method pattern is used to deal with the problem
 * of creating different "somehow related" objects.
 */
final class Factory
{
    private const NAME = 'NAME';
    private const PRICE = 'PRICE';
    private const CURRENCY = 'CURRENCY';
    private const CHANGE = 'CHANGE';
    private const CHANGE_PERCENT = 'CHANGE_PERCENT';
    private const TREND = 'TREND';
    private const NEWS = FoundMoreNews::NEWS;
    private const URL = 'URL';

    private array $env;

    public function __construct(array $env = [])
    {
        $this->env = $env;
    }

    public function createTickerNewsFacade(ChannelInterface ...$channels): StockTickerFacade
    {
        return new StockTickerFacade(
            new StockTickerFactory(
                HttpClient::create(),
                ...$channels
            )
        );
    }

    public function createAllSiteCrawlers(int $maxNewsToFetch): array
    {
        return [
            $this->createFinanceYahooSiteCrawler($maxNewsToFetch),
            $this->createBarronsSiteCrawler($maxNewsToFetch),
        ];
    }

    /**
     * @return ChannelInterface[]
     */
    public function createChannelByNames(array $channelNames): array
    {
        $flipped = array_flip($channelNames);
        $channels = [];

        if (isset($flipped[EmailChannel::class])) {
            $channels[] = $this->createEmailChannel();
        }

        if (isset($flipped[SlackChannel::class])) {
            $channels[] = $this->createSlackChannel();
        }

        return $channels;
    }

    private function createEmailChannel(string $templateName = 'email.twig'): EmailChannel
    {
        return new EmailChannel(
            $this->env['TO_ADDRESS'],
            new Mailer(new GmailSmtpTransport(
                $this->env['MAILER_USERNAME'],
                $this->env['MAILER_PASSWORD']
            )),
            new TwigTemplateGenerator(
                new Environment(new FilesystemLoader(__DIR__ . '/templates')),
                $templateName
            )
        );
    }

    private function createSlackChannel(string $templateName = 'slack.twig'): SlackChannel
    {
        return new SlackChannel(
            $this->env['SLACK_DESTINY_CHANNEL_ID'],
            new SlackHttpClient(HttpClient::create([
                'auth_bearer' => $this->env['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'],
            ])),
            new TwigTemplateGenerator(
                new Environment(new FilesystemLoader(__DIR__ . '/templates')),
                $templateName
            )
        );
    }

    private function createFinanceYahooSiteCrawler(int $maxNewsToFetch = 3): FinanceYahooSiteCrawler
    {
        return new FinanceYahooSiteCrawler([
            self::NAME => new JsonExtractor\QuoteSummaryStore\CompanyName(),
            self::PRICE => new JsonExtractor\QuoteSummaryStore\RegularMarketPrice(),
            self::CURRENCY => new JsonExtractor\QuoteSummaryStore\Currency(),
            self::CHANGE => new JsonExtractor\QuoteSummaryStore\RegularMarketChange(),
            self::CHANGE_PERCENT => new JsonExtractor\QuoteSummaryStore\RegularMarketChangePercent(),
            self::TREND => new JsonExtractor\QuoteSummaryStore\RecommendationTrend(),
            self::NEWS => new JsonExtractor\StreamStore\News($this->createNewsNormalizer($maxNewsToFetch)),
            self::URL => new JsonExtractor\RouteStore\ExternalUrl(),
        ]);
    }

    private function createBarronsSiteCrawler(int $maxNewsToFetch = 3): BarronsSiteCrawler
    {
        return new BarronsSiteCrawler([
            self::NEWS => new HtmlCrawler\News($this->createNewsNormalizer($maxNewsToFetch)),
        ]);
    }

    private function createNewsNormalizer(int $maxNewsToFetch = 3): NewsNormalizer
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

    private OutputInterface $output;

    public static function create(): self
    {
        return new self(new PrinterOutput());
    }

    private function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function printCrawResult(CrawlResult $crawlResult): void
    {
        if ($crawlResult->isEmpty()) {
            $this->output->writeln('Nothing new here...');

            return;
        }

        $this->output->writeln('~~~~~~~~~~~~~~~~~~~~~~~~~~');
        $this->output->writeln('~~~~~~ Crawl result ~~~~~~');
        $this->output->writeln('~~~~~~~~~~~~~~~~~~~~~~~~~~');

        foreach ($crawlResult->getCompaniesGroupedBySymbol() as $symbol => $company) {
            $this->output->writeln($symbol);

            foreach ($company->allInfo() as $key => $value) {
                $this->printfln('# %s => %s', $key, json_encode($value));
            }

            $this->output->writeln();
        }

        $this->output->writeln();
    }

    public function printNotifyResult(NotifyResult $notifyResult): void
    {
        if ($notifyResult->isEmpty()) {
            $this->output->writeln(' ~~~ Nothing new here...');

            return;
        }

        $this->output->writeln('===========================');
        $this->output->writeln('====== Notify result ======');
        $this->output->writeln('===========================');

        foreach ($notifyResult->conditionNamesGroupBySymbol() as $symbol => $conditionNames) {
            $this->output->writeln($symbol);
            $this->output->writeln('Conditions:');

            foreach ($conditionNames as $conditionName) {
                $this->printfln('  - %s', $conditionName);
            }

            $this->output->writeln();
        }

        $this->output->writeln();
    }

    public function sleepWithPrompt(int $sec): void
    {
        $this->output->writeln("Sleeping {$sec} seconds...");
        $len = mb_strlen((string) $sec);

        for ($i = $sec; $i > 0; $i--) {
            $this->output->write(sprintf("%0{$len}d\r", $i));
            sleep(1);
        }

        $this->output->writeln('Awake again!');
    }

    public function readSymbolsFromInput(array $argv): array
    {
        return count($argv) <= 1
            ? self::DEFAULT_SYMBOLS
            : array_slice($argv, 1);
    }

    /**
     * @psalm-suppress MissingParamType
     */
    public function printfln(string $fmt = '', ...$args): void
    {
        $this->output->writeln(sprintf($fmt, ...$args));
    }
}

interface OutputInterface
{
    public function write(string $str = ''): void;

    public function writeln(string $str = ''): void;
}

final class PrinterOutput implements OutputInterface
{
    public function writeln(string $str = ''): void
    {
        $this->write($str . PHP_EOL);
    }

    public function write(string $str = ''): void
    {
        print $str;
    }
}
