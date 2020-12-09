<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler;

use Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\CrawlerInterface;
use Chemaclass\FinanceYahoo\Crawler\HtmlSiteCrawler;
use Chemaclass\FinanceYahoo\Crawler\ReadModel\Site;
use Chemaclass\FinanceYahoo\Crawler\ReadModel\Ticker;
use Chemaclass\FinanceYahooTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;

final class HtmlCrawlerTest extends TestCase
{
    use WithFakeHttpClient;

    private const EXAMPLE_REQUEST_URL = 'https://example.url.com/%s/';

    /** @test */
    public function crawl(): void
    {
        $crawler = new HtmlSiteCrawler(
            self::EXAMPLE_REQUEST_URL,
            [
                'TheKey' => new class() implements CrawlerInterface {
                    public function crawlHtml(string $html): string
                    {
                        return 'example crawled text';
                    }
                },
            ]
        );

        $actual = $crawler->crawl(
            $this->mockHttpClient(),
            new Ticker('EXAMPLE_TICKER')
        );

        self::assertEquals(new Site([
            'TheKey' => 'example crawled text',
        ]), $actual);
    }
}
