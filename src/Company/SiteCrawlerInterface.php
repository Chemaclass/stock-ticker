<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company;

use Chemaclass\FinanceYahoo\Company\ReadModel\Site;
use Chemaclass\FinanceYahoo\Company\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface SiteCrawlerInterface
{
    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site;
}
