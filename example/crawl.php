#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\StockTicker\StockTickerFacade;
use Chemaclass\StockTicker\StockTickerFactory;
use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__ . '/autoload.php';

$symbols = (count($argv) <= 1)
    ? ['AMZN']
    : array_slice($argv, 1);

print sprintf('Crawling stock %s...', implode(', ', $symbols));

$facade = new StockTickerFacade(
    new StockTickerFactory(HttpClient::create())
);

$crawlResult = $facade->crawlStock([
    createFinanceYahooSiteCrawler(),
    createBarronsSiteCrawler(),
], $symbols);

print "~~~~~~ Crawl result ~~~~~~\n";

foreach ($crawlResult->getCompaniesGroupedBySymbol() as $symbol => $company) {
    print $symbol . PHP_EOL;

    foreach ($company->allInfo() as $key => $value) {
        print sprintf("# %s => %s\n", $key, json_encode($value));
    }
    print PHP_EOL;
}
