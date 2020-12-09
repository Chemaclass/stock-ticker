<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company;

use Chemaclass\FinanceYahoo\Crawler\SiteCrawlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CompanyCrawlerFactory implements CompanyCrawlerFactoryInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function createWithCrawlers(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler
    {
        return new CompanyCrawler($this->httpClient, ...$siteCrawlers);
    }
}
