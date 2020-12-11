#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\BuyHigherThanSell;
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
    // As a function or a callable class
    'AMZN' => new PolicyGroup([
        'high trend to buy' => static fn (Company $c): bool => $c->info('trend')->get('0')['buy'] > 25,
        'buy higher than sell' => new BuyHigherThanSell(),
    ]),
    // And combine them however you want
    'GOOG' => new PolicyGroup([
        'strongBuy higher than strongSell' => static function (Company $c): bool {
            $strongBuy = $c->info('trend')->get('0')['strongBuy'];
            $strongSell = $c->info('trend')->get('0')['strongSell'];

            return $strongBuy > $strongSell;
        },
    ]),
]);

dump($result->policiesGroupBySymbol());

print 'Done.' . PHP_EOL;
