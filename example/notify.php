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

print 'Sending notifications...' . PHP_EOL;

$result = sendNotifications($facade, [
    // You can define multiple policy conditions for the same Ticker.
    // As a function or a callable class, and combine them however you want.
    'AMZN' => new PolicyGroup([
        'High trend to buy' => static fn (Company $c): bool => $c->info('trend')->get('0')['buy'] > 25,
        new IsBuyHigherThanSell(),
    ]),
    'GOOG' => new PolicyGroup([
        'StrongBuy is higher than StrongSell' => static function (Company $c): bool {
            $strongBuy = $c->info('trend')->get('0')['strongBuy'];
            $strongSell = $c->info('trend')->get('0')['strongSell'];

            return $strongBuy > $strongSell;
        },
    ]),
]);

print 'Done.' . PHP_EOL;

printNotifyResult($result);
