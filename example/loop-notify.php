#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\Condition\FoundMoreNews;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\PolicyGroup;

require_once __DIR__ . '/autoload.php';

$facade = createFacade(
    createEmailChannel(),
);

while (true) {
    print 'Looking for news...' . PHP_EOL;

    $result = sendNotifications($facade, [
        'AMZN' => new PolicyGroup([new FoundMoreNews()]),
        'GOOG' => new PolicyGroup([new FoundMoreNews()]),
        'TSLA' => new PolicyGroup([new FoundMoreNews()]),
        'NFLX' => new PolicyGroup([new FoundMoreNews()]),
        'AAPL' => new PolicyGroup([new FoundMoreNews()]),
        'FB' => new PolicyGroup([new FoundMoreNews()]),
    ]);

    dump($result->conditionNamesGroupBySymbol());

    sleep(60);
}
