<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler;

use Chemaclass\FinanceYahoo\Crawler\CompanyCrawler;
use Chemaclass\FinanceYahoo\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\ReadModel\Company;
use Chemaclass\FinanceYahoo\ReadModel\Site;
use Chemaclass\FinanceYahoo\ReadModel\Ticker;
use Chemaclass\FinanceYahooTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CompanyCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    /** @test */
    public function crawlEmptyStock(): void
    {
        $finYahoo = new CompanyCrawler(
            $this->mockHttpClient(),
            $this->mockSiteCrawler('key1', 'value1')
        );

        self::assertEmpty($finYahoo->crawlStock());
    }

    /** @test */
    public function crawlStockForOneTickerAndMultipleCrawlers(): void
    {
        $finYahoo = new CompanyCrawler(
            $this->mockHttpClient(),
            $this->mockSiteCrawler('key1', 'value1'),
            $this->mockSiteCrawler('key2', 'value2')
        );

        $actual = $finYahoo->crawlStock(
            new Ticker('EXAMPLE_TICKER')
        );

        self::assertEquals([
            'EXAMPLE_TICKER' => new Company([
                'key1' => 'value1',
                'key2' => 'value2',
            ]),
        ], $actual);
    }

    /** @test */
    public function crawlStockForMultipleTickers(): void
    {
        $finYahoo = new CompanyCrawler(
            $this->mockHttpClient(),
            $this->mockSiteCrawler('key1', 'value1'),
        );

        $actual = $finYahoo->crawlStock(
            new Ticker('EXAMPLE_TICKER_1'),
            new Ticker('EXAMPLE_TICKER_2'),
        );

        self::assertEquals([
            'EXAMPLE_TICKER_1' => new Company([
                'key1' => 'value1',
            ]),
            'EXAMPLE_TICKER_2' => new Company([
                'key1' => 'value1',
            ]),
        ], $actual);
    }

    private function mockSiteCrawler(string $crawlerKey, string $extractedValue): SiteCrawlerInterface
    {
        return new class($crawlerKey, $extractedValue) implements SiteCrawlerInterface {
            private string $crawlerKey;

            private string $extractedValue;

            public function __construct(string $crawlerKey, string $extractedValue)
            {
                $this->crawlerKey = $crawlerKey;
                $this->extractedValue = $extractedValue;
            }

            public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
            {
                return new Site([$this->crawlerKey => $this->extractedValue]);
            }
        };
    }
}
