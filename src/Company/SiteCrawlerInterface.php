<?php

declare(strict_types=1);

namespace App\Company;

use App\Company\ReadModel\Site;
use App\Company\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface SiteCrawlerInterface
{
    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site;
}
