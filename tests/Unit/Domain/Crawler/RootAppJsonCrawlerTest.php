<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Domain\Crawler;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\Crawler\RootJsonSiteCrawler;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Site;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Ticker;
use Chemaclass\FinanceYahooTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;

final class RootAppJsonCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    private const RESPONSE_BODY = <<<BODY
any random string 1
root.App.main = {"key": {"sub-key": "example expected value"}};
any random string 2
BODY;

    public function testCrawlUsingNamedExtractor(): void
    {
        $crawler = new RootJsonSiteCrawler([
            'extractor name' => $this->stubJsonExtractor(),
        ]);

        $actual = $crawler->crawl(
            $this->mockHttpClient(self::RESPONSE_BODY),
            Ticker::withSymbol('EXAMPLE_TICKER')
        );

        self::assertEquals(new Site([
            'extractor name' => 'example expected value',
        ]), $actual);
    }

    public function testCrawlUsingExtractorWithoutName(): void
    {
        $jsonExtractor = $this->stubJsonExtractor();
        $crawler = new RootJsonSiteCrawler([$jsonExtractor]);

        $actual = $crawler->crawl(
            $this->mockHttpClient(self::RESPONSE_BODY),
            Ticker::withSymbol('EXAMPLE_TICKER')
        );

        self::assertEquals(new Site([
            get_class($jsonExtractor) => 'example expected value',
        ]), $actual);
    }

    private function stubJsonExtractor(): JsonExtractorInterface
    {
        return new class() implements JsonExtractorInterface {
            public function extractFromJson(array $json): ExtractedFromJson
            {
                return ExtractedFromJson::fromString($json['key']['sub-key']);
            }
        };
    }
}
