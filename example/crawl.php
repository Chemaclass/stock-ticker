#!/usr/local/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

$io = IO::create();
$symbols = $io->readSymbolsFromInput($argv);
$io->printfln('Crawling stock %s...', implode(', ', $symbols));

$crawlResult = TickerNews::create()
    ->crawlStock($symbols, $maxNewsToFetch = 3);

$io->printCrawResult($crawlResult);
