<?php

declare(strict_types=1);

namespace App;

use App\Company\CompanyCrawler;
use App\Company\ReadModel\Company;
use App\Company\ReadModel\TickerSymbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FinYahoo
{
    private HttpClientInterface $httpClient;

    /** @var CompanyCrawler[] */
    private array $companyCrawlers;

    public function __construct(
        HttpClientInterface $httpClient,
        CompanyCrawler ...$companyCrawlers
    ) {
        $this->httpClient = $httpClient;
        $this->companyCrawlers = $companyCrawlers;
    }

    /**
     * @psalm-return array<string,Company>
     */
    public function crawlStock(TickerSymbol ...$tickerSymbols): array
    {
        $result = [];

        foreach ($tickerSymbols as $symbol) {
            $crawlResultForSymbol = $this->crawlAllUrlsForSymbol($symbol);

            $result[$symbol->toString()] = new Company($this->flat($crawlResultForSymbol));
        }

        return $result;
    }

    private function crawlAllUrlsForSymbol(TickerSymbol $symbol): array
    {
        $result = [];

        foreach ($this->companyCrawlers as $crawler) {
            $result[] = $crawler->crawl($this->httpClient, $symbol);
        }

        return $result;
    }

    private function flat(array $crawlResultForSymbol): array
    {
        return array_merge(
            ...$this->normalizeCompanies(...$crawlResultForSymbol)
        );
    }

    private function normalizeCompanies(Company ...$companies): array
    {
        return array_map(
            static fn (Company $company) => $company->summary(),
            array_values($companies)
        );
    }
}
