<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler;

use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface SiteCrawlerInterface
{
    public function crawl(HttpClientInterface $httpClient, Symbol $symbol): Site;
}
