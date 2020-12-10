<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Site;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface SiteCrawlerInterface
{
    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site;
}
