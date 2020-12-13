<?php

declare(strict_types=1);

use Chemaclass\TickerNews\Domain\Crawler\CrawlResult;
use Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\BarronsSiteCrawler;
use Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlCrawler;
use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractor;
use Chemaclass\TickerNews\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\TickerNews\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\TickerNews\Domain\Notifier\Channel\Slack\SlackHttpClient;
use Chemaclass\TickerNews\Domain\Notifier\Channel\TwigTemplateGenerator;
use Chemaclass\TickerNews\Domain\Notifier\ChannelInterface;
use Chemaclass\TickerNews\Domain\Notifier\NotifierPolicy;
use Chemaclass\TickerNews\Domain\Notifier\NotifyResult;
use Chemaclass\TickerNews\TickerNewsFacade;
use Chemaclass\TickerNews\TickerNewsFactory;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

function createFacade(ChannelInterface ...$channels): TickerNewsFacade
{
    return new TickerNewsFacade(
        new TickerNewsFactory(
            HttpClient::create(),
            ...$channels
        )
    );
}

function sendNotifications(TickerNewsFacade $facade, array $policyGroupedBySymbol): NotifyResult
{
    $policy = new NotifierPolicy($policyGroupedBySymbol);
    $tickerSymbols = array_keys($policyGroupedBySymbol);

    return $facade->notify($policy, crawlStock($facade, $tickerSymbols));
}

function crawlStock(TickerNewsFacade $facade, array $tickerSymbols, int $maxNewsToFetch = 2): CrawlResult
{
    return $facade->crawlStock([
        createFinanceYahooSiteCrawler($maxNewsToFetch),
        createBarronsSiteCrawler($maxNewsToFetch),
    ], $tickerSymbols);
}

function createFinanceYahooSiteCrawler(int $maxNewsToFetch = 3): FinanceYahooSiteCrawler
{
    return new FinanceYahooSiteCrawler([
        'name' => new JsonExtractor\QuoteSummaryStore\CompanyName(),
        'price' => new JsonExtractor\QuoteSummaryStore\RegularMarketPrice(),
        'change' => new JsonExtractor\QuoteSummaryStore\RegularMarketChange(),
        'changePercent' => new JsonExtractor\QuoteSummaryStore\RegularMarketChangePercent(),
        'trend' => new JsonExtractor\QuoteSummaryStore\RecommendationTrend(),
        'news' => new JsonExtractor\StreamStore\News(new DateTimeZone('Europe/Berlin'), $maxNewsToFetch),
        'url' => new JsonExtractor\RouteStore\ExternalUrl(),
    ]);
}

function createBarronsSiteCrawler(int $maxNewsToFetch = 3): BarronsSiteCrawler
{
    return new BarronsSiteCrawler([
        'news' => new HtmlCrawler\News(new DateTimeZone('Europe/Berlin'), $maxNewsToFetch),
    ]);
}

function createEmailChannel(string $templateName = 'email.twig'): EmailChannel
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

function createSlackChannel(string $templateName = 'slack.twig'): SlackChannel
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

function printCrawResult(CrawlResult $crawlResult): void
{
    if ($crawlResult->isEmpty()) {
        println('Nothing new here...');

        return;
    }

    println('~~~~~~~~~~~~~~~~~~~~~~~~~~');
    println('~~~~~~ Crawl result ~~~~~~');
    println('~~~~~~~~~~~~~~~~~~~~~~~~~~');

    foreach ($crawlResult->getCompaniesGroupedBySymbol() as $symbol => $company) {
        println($symbol);

        foreach ($company->allInfo() as $key => $value) {
            printfln('# %s => %s', $key, json_encode($value));
        }
        println();
    }
    println();
}

function printNotifyResult(NotifyResult $notifyResult): void
{
    if ($notifyResult->isEmpty()) {
        println('Nothing new here...');

        return;
    }

    println('===========================');
    println('====== Notify result ======');
    println('===========================');

    foreach ($notifyResult->conditionNamesGroupBySymbol() as $symbol => $conditionNames) {
        println($symbol);
        println('Conditions:');

        foreach ($conditionNames as $conditionName) {
            printfln('  - %s', $conditionName);
        }
        println();
    }
    println();
}

function printfln(string $fmt = '', ...$args): void
{
    println(sprintf($fmt, ...$args));
}

function println(string $str = ''): void
{
    print $str . PHP_EOL;
}
