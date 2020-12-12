#!/usr/local/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

print 'Crawling stock...' . PHP_EOL;

$facade = createFacade();
$result = crawlStock($facade, ['AMZN'], $maxNewsToFetch = 5);

dump($result);

print 'Done.' . PHP_EOL;
