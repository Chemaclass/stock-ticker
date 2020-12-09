<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler;

use Chemaclass\FinanceYahoo\Crawler\ReadModel\Site;
use Chemaclass\FinanceYahoo\Crawler\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface SiteCrawlerInterface
{
    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site;
}
