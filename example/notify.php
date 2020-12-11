#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

require_once __DIR__ . '/autoload.php';

print 'Sending notifications...' . PHP_EOL;

$facade = createFacade(
    createEmailChannel(),
    createSlackChannel(),
);

$result = sendNotifications($facade, [
    // You can define multiple policies for the same Ticker
    'AMZN' => new PolicyGroup([
        'high trend to buy' => fn (Company $c): bool => $c->info('lastTrend')->get('buy') > 25,
        'some trend to sell' => fn (Company $c): bool => $c->info('lastTrend')->get('sell') > 0,
    ]),
    // And combine them however you want
    'GOOG' => new PolicyGroup([
        'strongBuy higher than strongSell' => function (Company $c): bool {
            $strongBuy = $c->info('lastTrend')->get('strongBuy');
            $strongSell = $c->info('lastTrend')->get('strongSell');

            return $strongBuy > $strongSell;
        },
    ]),
]);

dump($result->policiesGroupBySymbol());

print 'Done.' . PHP_EOL;
