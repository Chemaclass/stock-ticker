<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Integration;

use Chemaclass\FinanceYahoo\Company\CompanyCrawlerFactory;
use Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\CrawlerInterface;
use Chemaclass\FinanceYahoo\Crawler\HtmlSiteCrawler;
use Chemaclass\FinanceYahoo\Crawler\ReadModel\Company;
use Chemaclass\FinanceYahoo\FinanceYahooConfig;
use Chemaclass\FinanceYahoo\FinanceYahooFacade;
use Chemaclass\FinanceYahooTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;

final class FinanceYahooFacadeTest extends TestCase
{
    use WithFakeHttpClient;

    private const EXAMPLE_REQUEST_URL = 'https://example.url.com/%s/';

    /** @test */
    public function crawlStock(): void
    {
        $facade = (new FinanceYahooFacade(
            new FinanceYahooConfig('["AAA","BBB"]'),
            new CompanyCrawlerFactory($this->mockHttpClient())
        ));

        $actual = $facade->crawlStock(new HtmlSiteCrawler(
            self::EXAMPLE_REQUEST_URL,
            [
                'TheKey' => new class() implements CrawlerInterface {
                    public function crawlHtml(string $html): string
                    {
                        return 'crawled data';
                    }
                },
            ]
        ));

        self::assertEquals([
            'AAA' => new Company([
                'TheKey' => 'crawled data',
            ]),
            'BBB' => new Company([
                'TheKey' => 'crawled data',
            ]),
        ], $actual);
    }
}
