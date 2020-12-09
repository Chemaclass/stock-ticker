<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Company\CompanyBuilder;
use App\Company\CompanySummaryResult;
use App\Company\ReadModel\Company;
use App\Company\ReadModel\Summary;
use App\Company\ReadModel\TickerSymbol;
use App\Company\SummaryCrawlerInterface;
use App\FinYahoo;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class FinYahooTest extends TestCase
{
    /** @test */
    public function crawlEmptyStock(): void
    {
        $httpClient = $this->createHttpClientWithResponse('');
        $finYahoo = new FinYahoo($httpClient, new CompanyBuilder());

        self::assertEmpty($finYahoo->crawlStock());
    }

    /** @test */
    public function crawlStockForOneTicketSymbol(): void
    {
        $httpClient = $this->createHttpClientWithResponse('');
        $finYahoo = new FinYahoo($httpClient, new CompanyBuilder(
            new class() implements SummaryCrawlerInterface {
                public function crawlHtml(string $html): CompanySummaryResult
                {
                    return new CompanySummaryResult('summary-key', 'summary-value');
                }
            }
        ));

        $actual = $finYahoo->crawlStock(new TickerSymbol('EXAMPLE_TICKER'));

        self::assertEquals([
            'EXAMPLE_TICKER' => new Company(
                new Summary([
                    'summary-key' => 'summary-value',
                ])
            ),
        ], $actual);
    }

    private function createHttpClientWithResponse(string $responseBody): HttpClientInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')->willReturn($responseBody);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        return $httpClient;
    }
}
