<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Domain\Crawler;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\Crawler\RootAppJsonCrawler;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Site;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Ticker;
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
