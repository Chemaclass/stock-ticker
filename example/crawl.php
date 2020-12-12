#!/usr/local/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

$facade = createFacade();

print 'Crawling stock...' . PHP_EOL;
$result = crawlStock($facade, ['AMZN'], $maxNewsToFetch = 5);
print 'Done.' . PHP_EOL;

dump($result);
