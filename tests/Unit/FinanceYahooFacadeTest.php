<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit;

use Chemaclass\FinanceYahoo\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\FinanceYahooConfig;
use Chemaclass\FinanceYahoo\FinanceYahooFacade;
use Chemaclass\FinanceYahoo\FinanceYahooFactory;
use Chemaclass\FinanceYahoo\ReadModel\Company;
use Chemaclass\FinanceYahoo\ReadModel\Site;
use Chemaclass\FinanceYahoo\ReadModel\Ticker;
use Chemaclass\FinanceYahooTests\WithFakeHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FinanceYahooFacadeTest extends TestCase
{
    use WithFakeHttpClient;

    /** @test */
    public function crawlStock(): void
    {
        $facade = (new FinanceYahooFacade(
            new FinanceYahooConfig('["AAA","BBB"]'),
            new FinanceYahooFactory($this->mockHttpClient())
        ));

        $actual = $facade->crawlStock(new class() implements SiteCrawlerInterface {
            public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
            {
                return new Site(['TheKey' => 'crawled data']);
            }
        });

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
