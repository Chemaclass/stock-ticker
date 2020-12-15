#!/usr/local/bin/php
<?php

declare(strict_types=1);

use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\FoundMoreNews;
use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;

require_once __DIR__ . '/autoload.php';

$io = IO::create();

$symbols = $io->readSymbolsFromInput($argv);
$sleepingTimeInSeconds = 5;

$groupedPolicy = array_fill_keys(
    $symbols,
    new PolicyGroup([new FoundMoreNews()])
);

$channels = [
    EmailChannel::class,
    SlackChannel::class,
];

while (true) {
    $symbols = implode(', ', array_keys($groupedPolicy));
    $io->printfln('Looking for news in %s ...', $symbols);

    $result = TickerNews::create()
        ->sendNotifications($channels, $groupedPolicy, $maxNewsToFetch = 2);

    $io->printNotifyResult($result);
    $io->sleepWithPrompt($sleepingTimeInSeconds);
}
