#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\Condition\IsBuyHigherThanSell;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

require_once __DIR__ . '/autoload.php';

print 'Sending notifications...' . PHP_EOL;

$facade = createFacade(
    createEmailChannel(),
    createSlackChannel(),
);

$result = sendNotifications($facade, [
    // You can define multiple policy conditions for the same Ticker.
    // As a function or a callable class, and combine them however you want.
    'AMZN' => new PolicyGroup([
        'High trend to buy' => static fn (Company $c): bool => $c->info('trend')->get('0')['buy'] > 25,
        'Buy is higher than sell' => new IsBuyHigherThanSell(),
    ]),
    'GOOG' => new PolicyGroup([
        'StrongBuy is higher than StrongSell' => static function (Company $c): bool {
            $strongBuy = $c->info('trend')->get('0')['strongBuy'];
            $strongSell = $c->info('trend')->get('0')['strongSell'];

            return $strongBuy > $strongSell;
        },
    ]),
]);

dump($result->conditionNamesGroupBySymbol());

print 'Done.' . PHP_EOL;
