#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\ComparingTwoGroups;
use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\OlderWasFound;
use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\StockTicker\StockTickerFacade;
use Chemaclass\StockTicker\StockTickerFactory;
use Symfony\Component\HttpClient\HttpClient;

require_once __DIR__ . '/autoload.php';

$sleepingTimeInSeconds = 5;

$symbols = (count($argv) <= 1)
    ? ['GOOG']
    : array_slice($argv, 1);

$policyGroup = new PolicyGroup([
    'More news was found' => new OlderWasFound('NEWS'),
    'Buying is higher than selling' => new ComparingTwoGroups(
        'TREND',
        ['buy', 'strongBuy'],
        ['selling', 'strongSelling'],
    ),
]);

// Apply the same PolicyGroup to all symbols
$policy = new NotifierPolicy(
    array_fill_keys($symbols, $policyGroup)
);

$facade = new StockTickerFacade(
    new StockTickerFactory(
        HttpClient::create(),
        createEmailChannel(),
        createSlackChannel()
    )
);

$siteCrawlers = [
    createFinanceYahooSiteCrawler(),
    createBarronsSiteCrawler(),
];

while (true) {
    print sprintf("Looking for news in %s ...\n", implode(', ', $symbols));

    $crawlResult = $facade->crawlStock($siteCrawlers, $symbols);
    $notifyResult = $facade->notify($policy, $crawlResult);

    if ($notifyResult->isEmpty()) {
        print " ~~~ Nothing new here...\n";
        sleepWithPrompt($sleepingTimeInSeconds);

        continue;
    }

    print "====== Notify result ======\n";

    foreach ($notifyResult->conditionNamesGroupBySymbol() as $symbol => $conditionNames) {
        print $symbol . PHP_EOL;
        print "Conditions:\n";

        foreach ($conditionNames as $conditionName) {
            print sprintf("  - %s\n", $conditionName);
        }
        print PHP_EOL;
    }

    sleepWithPrompt($sleepingTimeInSeconds);
}

function sleepWithPrompt(int $sec): void
{
    print "Sleeping {$sec} seconds...\n";
    $len = mb_strlen((string) $sec);

    for ($i = $sec; $i > 0; $i--) {
        print sprintf("%0{$len}d\r", $i);
        sleep(1);
    }

    print "Awake again!\n";
}
