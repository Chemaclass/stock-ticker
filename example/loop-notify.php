#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\TickerNews\Domain\Notifier\Policy\Condition\FoundMoreNews;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;

require_once __DIR__ . '/autoload.php';

$facade = createFacade(
    createEmailChannel(),
);

$sleepingTimeInSeconds = 15;
$foundMoreNewsPolicy = new PolicyGroup([new FoundMoreNews()]);

$policyGroupedBySymbols = [
    'AMZN' => $foundMoreNewsPolicy,
    'GOOG' => $foundMoreNewsPolicy,
    'TSLA' => $foundMoreNewsPolicy,
    'NFLX' => $foundMoreNewsPolicy,
    'AAPL' => $foundMoreNewsPolicy,
    'FB' => $foundMoreNewsPolicy,
];

while (true) {
    $symbols = implode(', ', array_keys($policyGroupedBySymbols));
    printfln('Looking for news in %s ...', $symbols);

    $result = sendNotifications($facade, $policyGroupedBySymbols);

    printNotifyResult($result);
    sleepWithPrompt($sleepingTimeInSeconds);
}
