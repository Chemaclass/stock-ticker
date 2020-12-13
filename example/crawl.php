#!/usr/local/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

$symbols = ['AMZN', 'GOOG'];
printfln('Crawling stock %s...', implode(', ', $symbols));

$crawlResult = crawlStock(createFacade(), $symbols, $maxNewsToFetch = 3);

printCrawResult($crawlResult);
