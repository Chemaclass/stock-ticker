<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\StockTicker\Domain\ReadModel\Company;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Chemaclass\StockTicker\StockTickerFacade;
use Chemaclass\StockTicker\StockTickerFactory;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class StockTickerFacadeTest extends TestCase
{
    public function testCrawlStock(): void
    {
        $facade = $this->createStockTickerFacade(self::never());

        $siteCrawler = new class() implements SiteCrawlerInterface {
            public function crawl(HttpClientInterface $httpClient, Symbol $symbol): Site
            {
                return new Site([
                    'name' => ['Amazon.com, Inc.'],
                ]);
            }
        };

        $result = $facade->crawlStock(['AMZN']);
        $amazon = $result->getCompany('AMZN');

        self::assertEquals(Symbol::fromString('AMZN'), $amazon->symbol());
        self::assertSame(['Amazon.com, Inc.'], $amazon->info('name'));
    }

    public function testNotify(): void
    {
        $amazon = new Company(
            Symbol::fromString('AMZN'),
            ['trend' => ['buy' => 0, 'sell' => 10]],
        );

        $conditions = [
            $amazon->symbol()->toString() => new PolicyGroup([
                'high trend to buy' => static fn (Company $c) => $c->info('trend')['buy'] > 5,
                'high trend to sell' => static fn (Company $c) => $c->info('trend')['sell'] > 5,
            ]),
            'UNKNOWN' => new PolicyGroup([]),
        ];

        $facade = $this->createStockTickerFacade(self::once());

//        new CrawlResult([
//            $amazon->symbol()->toString() => $amazon,
//        ])
        $notifyResult = $facade->sendNotifications(
            $channelNames = [EmailChannel::class],
            $conditions
        );

        self::assertSame(['AMZN'], $notifyResult->symbols());
        self::assertSame($amazon, $notifyResult->companyForSymbol('AMZN'));
        self::assertSame(['high trend to sell'], $notifyResult->conditionNamesForSymbol('AMZN'));
    }

    private function createStockTickerFacade(InvocationOrder $channelSendExpected): StockTickerFacade
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->expects($channelSendExpected)->method('send');

        return new StockTickerFacade(
            new StockTickerFactory(new FakeStockTickerConfig())
        );
    }
}
