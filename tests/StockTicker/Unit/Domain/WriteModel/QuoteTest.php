<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\CompanyName;
use Chemaclass\StockTicker\Domain\WriteModel\MarketCap;
use Chemaclass\StockTicker\Domain\WriteModel\News;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use Chemaclass\StockTicker\Domain\WriteModel\RegularMarketChange;
use Chemaclass\StockTicker\Domain\WriteModel\RegularMarketChangePercent;
use Chemaclass\StockTicker\Domain\WriteModel\RegularMarketPrice;
use Chemaclass\StockTicker\Domain\WriteModel\Trend;
use PHPUnit\Framework\TestCase;

final class QuoteTest extends TestCase
{
    private Quote $quote;

    protected function setUp(): void
    {
        $this->quote = (new Quote())->fromArray([
            'symbol' => 'AMZN',
            'companyName' => [
                'shortName' => 'Short Company name, Inc.',
                'longName' => 'Long Company name, Inc.',
            ],
            'currency' => 'USD',
            'url' => 'https://example.url.com',
            'regularMarketPrice' => [
                'raw' => 629.999,
                'fmt' => '629.99',
            ],
            'regularMarketChange' => [
                'raw' => -3.2900085,
                'fmt' => '-3.29',
            ],
            'regularMarketChangePercent' => [
                'raw' => -1.8199171,
                'fmt' => '-1.82%',
            ],
            'marketCap' => [
                'raw' => 797834477568,
                'fmt' => '797.834B',
                'longFmt' => '797,834,477,568',
            ],
            'lastTrend' => [
                [
                    'period' => '0m',
                    'strongBuy' => 11,
                    'buy' => 12,
                    'hold' => 13,
                    'sell' => 14,
                    'strongSell' => 15,
                ],
                [
                    'period' => '-1m',
                    'strongBuy' => 21,
                    'buy' => 22,
                    'hold' => 23,
                    'sell' => 24,
                    'strongSell' => 25,
                ],
            ],
            'latestNews' => [
                [
                    'datetime' => 'example datetime',
                    'timezone' => 'example timezone',
                    'url' => 'example url',
                    'title' => 'example title',
                    'summary' => 'example summary',
                ],
            ],
        ]);
    }

    public function testCompanyName(): void
    {
        self::assertEquals((new CompanyName())->fromArray([
            'shortName' => 'Short Company name, Inc.',
            'longName' => 'Long Company name, Inc.',
        ]), $this->quote->getCompanyName());
    }

    public function testSymbol(): void
    {
        self::assertEquals('AMZN', $this->quote->getSymbol());
    }

    public function testCurrency(): void
    {
        self::assertEquals('USD', $this->quote->getCurrency());
    }

    public function testUrl(): void
    {
        self::assertEquals('https://example.url.com', $this->quote->getUrl());
    }

    public function testRegularMarketPrice(): void
    {
        self::assertEquals((new RegularMarketPrice())->fromArray([
            'raw' => 629.999,
            'fmt' => '629.99',
        ]), $this->quote->getRegularMarketPrice());
    }

    public function testRegularMarketChange(): void
    {
        self::assertEquals((new RegularMarketChange())->fromArray([
            'raw' => -3.2900085,
            'fmt' => '-3.29',
        ]), $this->quote->getRegularMarketChange());
    }

    public function testRegularMarketChangePercent(): void
    {
        self::assertEquals((new RegularMarketChangePercent())->fromArray([
            'raw' => -1.8199171,
            'fmt' => '-1.82%',
        ]), $this->quote->getRegularMarketChangePercent());
    }

    public function testMarketCap(): void
    {
        self::assertEquals((new MarketCap())->fromArray([
            'raw' => 797834477568,
            'fmt' => '797.834B',
            'longFmt' => '797,834,477,568',
        ]), $this->quote->getMarketCap());
    }

    public function testLastTrend(): void
    {
        self::assertEquals([
            (new Trend())
                ->setPeriod('0m')
                ->setStrongBuy(11)
                ->setBuy(12)
                ->setHold(13)
                ->setSell(14)
                ->setStrongSell(15),
            (new Trend())
                ->setPeriod('-1m')
                ->setStrongBuy(21)
                ->setBuy(22)
                ->setHold(23)
                ->setSell(24)
                ->setStrongSell(25),
        ], $this->quote->getLastTrend());
    }

    public function testLatestNews(): void
    {
        self::assertEquals([
            (new News())
                ->setDatetime('example datetime')
                ->setTimezone('example timezone')
                ->setUrl('example url')
                ->setTitle('example title')
                ->setSummary('example summary'),
        ], $this->quote->getLatestNews());
    }
}
