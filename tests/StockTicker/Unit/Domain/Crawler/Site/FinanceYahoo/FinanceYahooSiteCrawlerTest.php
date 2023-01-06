<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Crawler\Site\FinanceYahoo;

use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Chemaclass\StockTickerTests\Unit\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;

use function get_class;

final class FinanceYahooSiteCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    private const RESPONSE_BODY = <<<BODY
any random string 1
root.App.main = {"key": {"sub-key": "example expected value"}};
any random string 2
BODY;

    public function test_crawl_using_named_extractor(): void
    {
        $crawler = new FinanceYahooSiteCrawler([
            'extractor name' => $this->stubJsonExtractor(),
        ]);

        $actual = $crawler->crawl(
            $this->mockHttpClient(self::RESPONSE_BODY),
            Symbol::fromString('EXAMPLE_TICKER'),
        );

        self::assertEquals(new Site([
            'symbol' => 'EXAMPLE_TICKER',
            'extractor name' => ['example expected value'],
        ]), $actual);
    }

    public function test_crawl_using_extractor_without_name(): void
    {
        $jsonExtractor = $this->stubJsonExtractor();
        $crawler = new FinanceYahooSiteCrawler([$jsonExtractor]);

        $actual = $crawler->crawl(
            $this->mockHttpClient(self::RESPONSE_BODY),
            Symbol::fromString('EXAMPLE_TICKER'),
        );

        self::assertEquals(new Site([
            'symbol' => 'EXAMPLE_TICKER',
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
