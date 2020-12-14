#!/usr/local/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

$symbols = IO::readSymbolsFromInput($argv);
IO::printfln('Crawling stock %s...', implode(', ', $symbols));

$crawlResult = TickerNews::crawlStock($symbols, $maxNewsToFetch = 3);

IO::printCrawResult($crawlResult);
