<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler;

use Chemaclass\FinanceYahoo\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Crawler\RootAppJsonCrawler;
use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;
use Chemaclass\FinanceYahoo\ReadModel\Site;
use Chemaclass\FinanceYahoo\ReadModel\Ticker;
use Chemaclass\FinanceYahooTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;

final class RootAppJsonCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    /** @test */
    public function crawl(): void
    {
        $crawler = new RootAppJsonCrawler(
            new class() implements JsonExtractorInterface {
                public static function name(): string
                {
                    return 'TheKey';
                }

                public function extractFromJson(array $json): ExtractedFromJson
                {
                    return ExtractedFromJson::fromString($json['key']['sub-key']);
                }
            }
        );

        $responseBody = <<<BODY
any random string 1
root.App.main = {"key": {"sub-key": "example value"}};
any random string 2
BODY;
        $actual = $crawler->crawl(
            $this->mockHttpClient($responseBody),
            new Ticker('EXAMPLE_TICKER')
        );

        self::assertEquals(new Site([
            'TheKey' => 'example value',
        ]), $actual);
    }
}
