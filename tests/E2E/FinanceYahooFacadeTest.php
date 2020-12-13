<?php

declare(strict_types=1);

namespace Chemaclass\TickerNewsTests\E2E;

use Chemaclass\TickerNews\Domain\Crawler\CrawlResult;
use Chemaclass\TickerNews\Domain\Crawler\FinanceYahooSiteCrawler;
use Chemaclass\TickerNews\Domain\Crawler\JsonExtractor\QuoteSummaryStore\CompanyName;
use Chemaclass\TickerNews\Domain\Notifier\ChannelInterface;
use Chemaclass\TickerNews\Domain\Notifier\NotifierPolicy;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\TickerNews\Domain\ReadModel\Company;
use Chemaclass\TickerNews\Domain\ReadModel\ExtractedFromJson;
use Chemaclass\TickerNews\Domain\ReadModel\Ticker;
use Chemaclass\TickerNews\TickerNewsFacade;
use Chemaclass\TickerNews\TickerNewsFactory;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

final class TickerNewsFacadeTest extends TestCase
{
    public function testCrawlStock(): void
    {
        $facade = $this->createTickerNewsFacade(self::never());

        $siteCrawler = new FinanceYahooSiteCrawler([
            'name' => new CompanyName(),
        ]);

        $result = $facade->crawlStock([$siteCrawler], ['AMZN']);
        $amazon = $result->getCompany('AMZN');

        self::assertEquals(Ticker::withSymbol('AMZN'), $amazon->ticker());
        self::assertEquals('Amazon.com, Inc.', $amazon->info('name'));
    }

    public function testNotify(): void
    {
        $amazon = new Company(
            Ticker::withSymbol('AMZN'),
            ['trend' => ExtractedFromJson::fromArray(['buy' => 0, 'sell' => 10])],
        );

        $google = new Company(
            Ticker::withSymbol('GOOG'),
            ['trend' => ExtractedFromJson::fromArray(['buy' => 10, 'sell' => 0])],
        );

        $policy = new NotifierPolicy([
            $amazon->ticker()->symbol() => new PolicyGroup([
                'high trend to buy' => static fn (Company $c) => $c->info('trend')->get('buy') > 5,
                'high trend to sell' => static fn (Company $c) => $c->info('trend')->get('sell') > 5,
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
