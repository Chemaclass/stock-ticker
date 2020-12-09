<?php

declare(strict_types=1);

namespace App;

use App\Company\CompanyCrawler;
use App\Company\ReadModel\Company;
use App\Company\ReadModel\Ticker;
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
    public function crawlStock(Ticker ...$tickers): array
    {
        $result = [];

        foreach ($tickers as $ticker) {
            $crawlResults = $this->crawlAllUrlsForTicker($ticker);

            $result[$ticker->symbol()] = new Company($this->flat($crawlResults));
        }

        return $result;
    }

    private function crawlAllUrlsForTicker(Ticker $ticker): array
    {
        return array_map(
            fn (CompanyCrawler $crawler): Company => $crawler->crawl($this->httpClient, $ticker),
            $this->companyCrawlers
        );
    }

    private function flat(array $companies): array
    {
        return array_merge(
            ...$this->normalizeCompanies(...$companies)
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
