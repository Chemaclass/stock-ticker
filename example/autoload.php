<?php

declare(strict_types=1);

use Chemaclass\FinanceYahoo\Domain\Crawler\CrawlResult;
use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor;
use Chemaclass\FinanceYahoo\Domain\Crawler\RootAppJsonCrawler;
use Chemaclass\FinanceYahoo\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\FinanceYahoo\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\FinanceYahoo\Domain\Notifier\Channel\Slack\SlackHttpClient;
use Chemaclass\FinanceYahoo\Domain\Notifier\Channel\TwigTemplateGenerator;
use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\NotifierPolicy;
use Chemaclass\FinanceYahoo\FinanceYahooFacade;
use Chemaclass\FinanceYahoo\FinanceYahooFactory;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();

function createFacade(ChannelInterface ...$channels): FinanceYahooFacade
{
    return new FinanceYahooFacade(
        new FinanceYahooFactory(
            HttpClient::create(),
            ...$channels
        )
    );
}

function sendNotifications(FinanceYahooFacade $facade, array $policyGroupedBySymbol): NotifyResult
{
    $policy = new NotifierPolicy($policyGroupedBySymbol);
    $tickerSymbols = array_keys($policyGroupedBySymbol);

    return $facade->notify($policy, crawlStock($facade, $tickerSymbols));
}

function crawlStock(FinanceYahooFacade $facade, array $tickerSymbols): CrawlResult
{
    $siteCrawler = new RootAppJsonCrawler([
        'name' => new JsonExtractor\CompanyNameExtractor(),
        'price' => new JsonExtractor\PriceExtractor(),
        'trend' => new JsonExtractor\TrendExtractor(),
        'news' => new JsonExtractor\NewsExtractor(),
    ]);

    return $facade->crawlStock([$siteCrawler], $tickerSymbols);
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
