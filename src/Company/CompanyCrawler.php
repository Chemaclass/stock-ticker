<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company;

use Chemaclass\FinanceYahoo\Crawler\ReadModel\Company;
use Chemaclass\FinanceYahoo\Crawler\ReadModel\Site;
use Chemaclass\FinanceYahoo\Crawler\ReadModel\Ticker;
use Chemaclass\FinanceYahoo\Crawler\SiteCrawlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CompanyCrawler
{
    private HttpClientInterface $httpClient;

    /** @var SiteCrawlerInterface[] */
    private array $siteCrawlers;

    public function __construct(
        HttpClientInterface $httpClient,
        SiteCrawlerInterface ...$siteCrawlers
    ) {
        $this->httpClient = $httpClient;
        $this->siteCrawlers = $siteCrawlers;
    }

    /**
     * @psalm-return array<string,Company>
     */
    public function crawlStock(Ticker ...$tickers): array
    {
        $result = [];

        foreach ($tickers as $ticker) {
            $sites = $this->crawlAllSitesForTicker($ticker);

            $result[$ticker->symbol()] = new Company($this->flat(...$sites));
        }

        return $result;
    }

    /**
     * @return Site[]
     */
    private function crawlAllSitesForTicker(Ticker $ticker): array
    {
        return array_map(
            fn (SiteCrawlerInterface $crawler): Site => $crawler->crawl($this->httpClient, $ticker),
            $this->siteCrawlers
        );
    }

    private function flat(Site ...$sites): array
    {
        return array_merge(
            ...$this->normalizeSites(...$sites)
        );
    }

    private function normalizeSites(Site ...$sites): array
    {
        return array_map(
            static fn (Site $site): array => $site->crawled(),
            array_values($sites)
        );
    }
}
