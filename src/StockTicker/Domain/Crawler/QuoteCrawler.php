<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler;

use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapperInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class QuoteCrawler implements QuoteCrawlerInterface
{
    private HttpClientInterface $httpClient;

    private CrawledInfoMapperInterface $crawledInfoMapper;

    /** @var SiteCrawlerInterface[] */
    private array $siteCrawlers;

    public function __construct(
        HttpClientInterface $httpClient,
        CrawledInfoMapperInterface $crawledInfoMapper,
        SiteCrawlerInterface ...$siteCrawlers
    ) {
        $this->httpClient = $httpClient;
        $this->crawledInfoMapper = $crawledInfoMapper;
        $this->siteCrawlers = $siteCrawlers;
    }

    public function crawlStock(string ...$symbolStrings): CrawlResult
    {
        $result = [];
        $symbols = $this->mapSymbols(...$symbolStrings);

        foreach ($symbols as $symbol) {
            $sites = $this->crawlAllSitesForSymbol($symbol);
            $info = $this->flat(...$sites);

            $result[$symbol->toString()] = $this->crawledInfoMapper->mapQuote($info);
        }

        return new CrawlResult($result);
    }

    /**
     * @return Symbol[]
     */
    private function mapSymbols(string ...$symbolStrings): array
    {
        return array_map(
            static fn (string $symbol) => Symbol::fromString($symbol),
            $symbolStrings
        );
    }

    /**
     * @return Site[]
     */
    private function crawlAllSitesForSymbol(Symbol $symbol): array
    {
        return array_map(
            fn (SiteCrawlerInterface $crawler): Site => $crawler->crawl($this->httpClient, $symbol),
            $this->siteCrawlers
        );
    }

    /**
     * Combine all sites keys values, merging the values from the shared keys in the same array.
     */
    private function flat(Site ...$sites): array
    {
        return array_merge_recursive(...$this->normalizeSites(...$sites));
    }

    /**
     * @return list<array<array-key, mixed>>
     */
    private function normalizeSites(Site ...$sites): array
    {
        return array_map(
            static fn (Site $site): array => $site->crawled(),
            array_values($sites)
        );
    }
}
