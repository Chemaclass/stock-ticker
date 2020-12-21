<?php

declare(strict_types=1);

use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\BarronsSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\HtmlCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizer;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\HttpSlackClient;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\TwigTemplateGenerator;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

/*
 * ======================================================
 * This `autoload.php` and these functions are just for
 * the example usage.
 * ======================================================
 */

function createFinanceYahooSiteCrawler(int $maxNewsToFetch = 2): FinanceYahooSiteCrawler
{
    return new FinanceYahooSiteCrawler([
        'NAME' => new JsonExtractor\QuoteSummaryStore\CompanyName(),
        'PRICE' => new JsonExtractor\QuoteSummaryStore\RegularMarketPrice(),
        'CURRENCY' => new JsonExtractor\QuoteSummaryStore\Currency(),
        'CHANGE' => new JsonExtractor\QuoteSummaryStore\RegularMarketChange(),
        'CHANGE_PERCENT' => new JsonExtractor\QuoteSummaryStore\RegularMarketChangePercent(),
        'TREND' => new JsonExtractor\QuoteSummaryStore\RecommendationTrend(),
        'NEWS' => new JsonExtractor\StreamStore\News(new NewsNormalizer(new DateTimeZone('Europe/Berlin'), $maxNewsToFetch)),
        'URL' => new JsonExtractor\RouteStore\ExternalUrl(),
    ]);
}

function createBarronsSiteCrawler(int $maxNewsToFetch = 2): BarronsSiteCrawler
{
    return new BarronsSiteCrawler([
        'NEWS' => new HtmlCrawler\News(new NewsNormalizer(new DateTimeZone('Europe/Berlin'), $maxNewsToFetch)),
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
        new HttpSlackClient(HttpClient::create([
            'auth_bearer' => $_ENV['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'],
        ])),
        new TwigTemplateGenerator(
            new Environment(new FilesystemLoader(__DIR__ . '/templates')),
            $templateName
        )
    );
}
