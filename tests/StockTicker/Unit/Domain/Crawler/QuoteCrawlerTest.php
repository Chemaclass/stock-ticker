<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Crawler;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapper;
use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapperInterface;
use Chemaclass\StockTicker\Domain\Crawler\QuoteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Chemaclass\StockTicker\Domain\WriteModel\CompanyName;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use Chemaclass\StockTickerTests\Unit\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class QuoteCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    public function testEmptyStockWhenNoTickerWasProvided(): void
    {
        $companyCrawler = new QuoteCrawler(
            $this->mockHttpClient(),
            $this->createCrawledInfoMapper(),
            $this->mockSiteCrawler('key1', 'value1')
        );

        self::assertEquals(new CrawlResult([]), $companyCrawler->crawlStock());
    }

    public function testCrawlStockForOneTickerAndMultipleCrawlers(): void
    {
        $companyCrawler = new QuoteCrawler(
            $this->mockHttpClient(),
            $this->createCrawledInfoMapper(static function (array $info): array {
                $info['companyName']['longName'] = $info['longName'];

                return $info;
            }),
            $this->mockSiteCrawler('symbol', 'SYMBOL'),
            $this->mockSiteCrawler('longName', 'Company Name, Inc.')
        );

        $actual = $companyCrawler->crawlStock('SYMBOL');

        self::assertEquals(new CrawlResult([
            'SYMBOL' => (new Quote())
                ->setSymbol('SYMBOL')
                ->setCompanyName((new CompanyName())
                    ->setLongName('Company Name, Inc.')),
        ]), $actual);
    }

    public function tCrawlStockForMultipleTickerSymbols(): void
    {
        $companyCrawler = new QuoteCrawler(
            $this->mockHttpClient(),
            $this->createCrawledInfoMapper(),
            $this->mockSiteCrawler('key1', 'value1'),
        );

        $actual = $companyCrawler->crawlStock('SYMBOL_1', 'SYMBOL_2');

        self::assertEquals(new CrawlResult([
            'SYMBOL_1' => (new Quote())
                ->setSymbol('SYMBOL_1'),
            'SYMBOL_2' => (new Quote())
                ->setSymbol('SYMBOL_2'),
        ]), $actual);
    }

    private function createCrawledInfoMapper(?callable $map = null): CrawledInfoMapperInterface
    {
        return new CrawledInfoMapper($map);
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
