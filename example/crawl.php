#!/usr/local/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

$facade = createFacade();

println('Crawling stock...');
$crawlResult = crawlStock($facade, ['AMZN'], $maxNewsToFetch = 5);
println('Done.');
printCrawResult($crawlResult);
