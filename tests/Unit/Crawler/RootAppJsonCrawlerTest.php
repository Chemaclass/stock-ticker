<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler;

use Chemaclass\FinanceYahoo\Crawler\ReadModel\Site;
use Chemaclass\FinanceYahoo\Crawler\ReadModel\Ticker;
use Chemaclass\FinanceYahoo\Crawler\RootAppJsonCrawler;
use Chemaclass\FinanceYahooTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;

final class RootAppJsonCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    private const EXAMPLE_REQUEST_URL = 'https://example.url.com/%s/';

    /** @test */
    public function crawl(): void
    {
        $crawler = new RootAppJsonCrawler(
            self::EXAMPLE_REQUEST_URL,
            fn (array $json): array => [
                'TheKey' => $json['key']['sub-key'],
            ]
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
