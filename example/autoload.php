<?php

declare(strict_types=1);

use Chemaclass\FinanceYahoo\Domain\Crawler\CrawlResult;
use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor;
use Chemaclass\FinanceYahoo\Domain\Crawler\RootAppJsonCrawler;
use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\NotifierPolicy;
use Chemaclass\FinanceYahoo\FinanceYahooFacade;
use Chemaclass\FinanceYahoo\FinanceYahooFactory;
use Symfony\Component\HttpClient\HttpClient;

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
