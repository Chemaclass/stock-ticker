<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\Crawler\CompanyCrawler;
use Chemaclass\FinanceYahoo\Domain\Crawler\SiteCrawlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FinanceYahooFactory implements FinanceYahooFactoryInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler
    {
        return new CompanyCrawler($this->httpClient, ...$siteCrawlers);
    }
}
