<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler;

use Chemaclass\TickerNews\Domain\ReadModel\Site;
use Chemaclass\TickerNews\Domain\ReadModel\Symbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface SiteCrawlerInterface
{
    public function crawl(HttpClientInterface $httpClient, Symbol $symbol): Site;
}
