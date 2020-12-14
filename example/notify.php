#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\TickerNews\Domain\Notifier\Policy\Condition\FoundMoreNews;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;

require_once __DIR__ . '/autoload.php';

$channels = [
    Factory::createEmailChannel(),
    Factory::createSlackChannel(),
];

$sleepingTimeInSeconds = 5;
$symbols = IO::readSymbolsFromInput($argv);

$groupedPolicy = array_fill_keys(
    $symbols,
    new PolicyGroup([new FoundMoreNews()])
);

while (true) {
    $symbols = implode(', ', array_keys($groupedPolicy));
    IO::printfln('Looking for news in %s ...', $symbols);

    $result = TickerNews::sendNotifications($channels, $groupedPolicy);

    IO::printNotifyResult($result);
    IO::sleepWithPrompt($sleepingTimeInSeconds);
}
