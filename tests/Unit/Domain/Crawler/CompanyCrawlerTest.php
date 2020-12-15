<?php

declare(strict_types=1);

namespace Chemaclass\TickerNewsTests\Unit\Domain\Crawler;

use Chemaclass\TickerNews\Domain\Crawler\CompanyCrawler;
use Chemaclass\TickerNews\Domain\Crawler\CrawlResult;
use Chemaclass\TickerNews\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\TickerNews\Domain\ReadModel\Company;
use Chemaclass\TickerNews\Domain\ReadModel\Site;
use Chemaclass\TickerNews\Domain\ReadModel\Symbol;
use Chemaclass\TickerNewsTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CompanyCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    public function testEmptyStockWhenNoTickerWasProvided(): void
    {
        $companyCrawler = new CompanyCrawler(
            $this->mockHttpClient(),
            $this->mockSiteCrawler('key1', 'value1')
        );

        self::assertEquals(new CrawlResult([]), $companyCrawler->crawlStock());
    }

    public function testCrawlStockForOneTickerAndMultipleCrawlers(): void
    {
        $companyCrawler = new CompanyCrawler(
            $this->mockHttpClient(),
            $this->mockSiteCrawler('key1', 'value1'),
            $this->mockSiteCrawler('key2', 'value2')
        );

        $actual = $companyCrawler->crawlStock('SYMBOL');

        self::assertEquals(new CrawlResult([
            'SYMBOL' => new Company(
                Symbol::fromString('SYMBOL'),
                [
                    'key1' => 'value1',
                    'key2' => 'value2',
                ]
            ),
        ]), $actual);
    }

    public function testCrawlStockForMultipleTickerSymbols(): void
    {
        $companyCrawler = new CompanyCrawler(
            $this->mockHttpClient(),
            $this->mockSiteCrawler('key1', 'value1'),
        );

        $actual = $companyCrawler->crawlStock('SYMBOL_1', 'SYMBOL_2');

        self::assertEquals(new CrawlResult([
            'SYMBOL_1' => new Company(
                Symbol::fromString('SYMBOL_1'),
                [
                    'key1' => 'value1',
                ]
            ),
            'SYMBOL_2' => new Company(
                Symbol::fromString('SYMBOL_2'),
                [
                    'key1' => 'value1',
                ]
            ),
        ]), $actual);
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

            public function crawl(HttpClientInterface $httpClient, Symbol $symbol): Site
            {
                return new Site([$this->crawlerKey => $this->extractedValue]);
            }
        };
    }
}
