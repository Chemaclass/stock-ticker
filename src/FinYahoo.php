<?php

declare(strict_types=1);

namespace App;

use App\Company\CompanyBuilder;
use App\Company\ReadModel\Company;
use App\Company\ReadModel\TickerSymbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FinYahoo
{
    private const BASE_REQUEST_URL = 'https://finance.yahoo.com/quote/';

    private const REQUEST_METHOD = 'GET';

    private HttpClientInterface $httpClient;

    private CompanyBuilder $companyBuilder;

    public function __construct(
        HttpClientInterface $httpClient,
        CompanyBuilder $companyBuilder
    ) {
        $this->httpClient = $httpClient;
        $this->companyBuilder = $companyBuilder;
    }

    /**
     * @psalm-return array<string,Company>
     */
    public function crawlStock(TickerSymbol ...$tickerSymbols): array
    {
        $result = [];

        foreach ($tickerSymbols as $tickerSymbol) {
            $result[$tickerSymbol->toString()] = $this->crawlTickerSymbol($tickerSymbol);
        }

        return $result;
    }

    private function crawlTickerSymbol(TickerSymbol $tickerSymbol): Company
    {
        $url = self::BASE_REQUEST_URL . $tickerSymbol->toString();
        $response = $this->httpClient->request(self::REQUEST_METHOD, $url);

        return $this->companyBuilder->buildFromHtml($response->getContent());
    }
}
