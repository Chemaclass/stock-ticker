<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Crawler\Site\FinanceYahoo;

use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Chemaclass\StockTickerTests\Unit\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;

final class FinanceYahooSiteCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    private const RESPONSE_BODY = <<<BODY
any random string 1
root.App.main = {"key": {"sub-key": "example expected value"}};
any random string 2
BODY;

    public function testCrawlUsingNamedExtractor(): void
    {
        $crawler = new FinanceYahooSiteCrawler([
            'extractor name' => $this->stubJsonExtractor(),
        ]);

        $actual = $crawler->crawl(
            $this->mockHttpClient(self::RESPONSE_BODY),
            Symbol::fromString('EXAMPLE_TICKER')
        );

        self::assertEquals(new Site([
            'extractor name' => ['example expected value'],
        ]), $actual);
    }

    public function testCrawlUsingExtractorWithoutName(): void
    {
        $jsonExtractor = $this->stubJsonExtractor();
        $crawler = new FinanceYahooSiteCrawler([$jsonExtractor]);

        $actual = $crawler->crawl(
            $this->mockHttpClient(self::RESPONSE_BODY),
            Symbol::fromString('EXAMPLE_TICKER')
        );

        self::assertEquals(new Site([
            get_class($jsonExtractor) => ['example expected value'],
        ]), $actual);
    }

    private function stubJsonExtractor(): JsonExtractorInterface
    {
        return new class() implements JsonExtractorInterface {
            public function extractFromJson(array $json): array
            {
                return [
                    $json['key']['sub-key'],
                ];
            }
        };
    }
}
