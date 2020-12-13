<?php

declare(strict_types=1);

namespace Chemaclass\TickerNewsTests\Unit;

use Chemaclass\TickerNews\Domain\Crawler\CrawlResult;
use Chemaclass\TickerNews\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\TickerNews\Domain\Notifier\ChannelInterface;
use Chemaclass\TickerNews\Domain\Notifier\NotifierPolicy;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\TickerNews\Domain\ReadModel\Company;
use Chemaclass\TickerNews\Domain\ReadModel\Site;
use Chemaclass\TickerNews\Domain\ReadModel\Ticker;
use Chemaclass\TickerNews\TickerNewsFacade;
use Chemaclass\TickerNews\TickerNewsFactory;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TickerNewsFacadeTest extends TestCase
{
    public function testCrawlStock(): void
    {
        $facade = $this->createTickerNewsFacade(self::never());

        $siteCrawler = new class() implements SiteCrawlerInterface {
            public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
            {
                return new Site([
                    'name' => 'Amazon.com, Inc.',
                ]);
            }
        };

        $result = $facade->crawlStock([$siteCrawler], ['AMZN']);
        $amazon = $result->getCompany('AMZN');

        self::assertEquals(Ticker::withSymbol('AMZN'), $amazon->ticker());
        self::assertEquals('Amazon.com, Inc.', $amazon->info('name'));
    }

    public function testNotify(): void
    {
        $amazon = new Company(
            Ticker::withSymbol('AMZN'),
            ['trend' => ['buy' => 0, 'sell' => 10]],
        );

        $google = new Company(
            Ticker::withSymbol('GOOG'),
            ['trend' => ['buy' => 10, 'sell' => 0]],
        );

        $policy = new NotifierPolicy([
            $amazon->ticker()->symbol() => new PolicyGroup([
                'high trend to buy' => static fn (Company $c) => $c->info('trend')['buy'] > 5,
                'high trend to sell' => static fn (Company $c) => $c->info('trend')['sell'] > 5,
            ]),
            'UNKNOWN' => new PolicyGroup([]),
        ]);

        $facade = $this->createTickerNewsFacade(self::once());

        $notifyResult = $facade->notify($policy, new CrawlResult([
            $amazon->ticker()->symbol() => $amazon,
            $google->ticker()->symbol() => $google,
        ]));

        self::assertSame(['AMZN'], $notifyResult->symbols());
        self::assertSame($amazon, $notifyResult->companyForSymbol('AMZN'));
        self::assertSame(['high trend to sell'], $notifyResult->conditionNamesForSymbol('AMZN'));
    }

    private function createTickerNewsFacade(InvocationOrder $channelSendExpected): TickerNewsFacade
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->expects($channelSendExpected)->method('send');

        return new TickerNewsFacade(
            new TickerNewsFactory(
                HttpClient::create(),
                $channel
            )
        );
    }
}
