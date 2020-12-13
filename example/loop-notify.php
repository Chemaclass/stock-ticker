#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\TickerNews\Domain\Notifier\Policy\Condition\FoundMoreNews;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;

require_once __DIR__ . '/autoload.php';

$facade = createFacade(
    createEmailChannel(),
);

while (true) {
    println('Looking for news...');

    $result = sendNotifications($facade, [
        'AMZN' => new PolicyGroup([new FoundMoreNews()]),
        'GOOG' => new PolicyGroup([new FoundMoreNews()]),
        'TSLA' => new PolicyGroup([new FoundMoreNews()]),
        'NFLX' => new PolicyGroup([new FoundMoreNews()]),
        'AAPL' => new PolicyGroup([new FoundMoreNews()]),
        'FB' => new PolicyGroup([new FoundMoreNews()]),
    ]);

    printNotifyResult($result);

    println('Sleeping...');
    sleep(60);
    println('Awake again!');
}
