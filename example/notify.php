#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\TickerNews\Domain\Notifier\Policy\Condition\IsBuyHigherThanSell;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\TickerNews\Domain\ReadModel\Company;

require_once __DIR__ . '/autoload.php';

$facade = createFacade(
    createEmailChannel(),
    createSlackChannel(),
);

$policyGroupedBySymbols = [
    // You can define multiple policy conditions for the same Ticker.
    // As a function or a callable class, and combine them however you want.
    'AMZN' => new PolicyGroup([
        'High trend to buy' => static fn (Company $c): bool => $c->info('trend')['0']['buy'] > 25,
        new IsBuyHigherThanSell(),
    ]),
    'GOOG' => new PolicyGroup([
        'StrongBuy is higher than StrongSell' => static function (Company $c): bool {
            $strongBuy = $c->info('trend')['0']['strongBuy'];
            $strongSell = $c->info('trend')['0']['strongSell'];

            return $strongBuy > $strongSell;
        },
    ]),
];

$symbols = implode(', ', array_keys($policyGroupedBySymbols));
printfln('Looking for news in %s ...', $symbols);

$result = sendNotifications($facade, $policyGroupedBySymbols);

printNotifyResult($result);
