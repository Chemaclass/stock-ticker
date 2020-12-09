<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Company\CompanyBuilder;
use App\Company\Crawler\CrawlerInterface;
use App\Company\ReadModel\Company;
use App\Company\ReadModel\TickerSymbol;
use App\FinYahoo;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class FinYahooTest extends TestCase
{
    /** @test */
    public function crawlEmptyStock(): void
    {
        $httpClient = $this->mockHttpClient();
        $finYahoo = new FinYahoo($httpClient, new CompanyBuilder([]));

        self::assertEmpty($finYahoo->crawlStock());
    }

    /** @test */
    public function crawlStockForOneTicketSymbol(): void
    {
        $httpClient = $this->mockHttpClient();
        $finYahoo = new FinYahoo($httpClient, new CompanyBuilder(
            [
                'summary-key' => new class() implements CrawlerInterface {
                    public function crawlHtml(string $html): string
                    {
                        return 'summary-value';
                    }
                },
            ]
        ));

        $actual = $finYahoo->crawlStock(new TickerSymbol('EXAMPLE_TICKER'));

        self::assertEquals([
            'EXAMPLE_TICKER' => new Company(
                [
                    'summary-key' => 'summary-value',
                ]
            ),
        ], $actual);
    }

    private function mockHttpClient(string $responseBody = ''): HttpClientInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')->willReturn($responseBody);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        return $httpClient;
    }
}
