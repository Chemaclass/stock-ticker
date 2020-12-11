<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\E2E;

use Chemaclass\FinanceYahoo\Domain\Crawler\CrawlResult;
use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\QuoteSummaryStore\CompanyName;
use Chemaclass\FinanceYahoo\Domain\Crawler\RootJsonSiteCrawler;
use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifierPolicy;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Ticker;
use Chemaclass\FinanceYahoo\FinanceYahooFacade;
use Chemaclass\FinanceYahoo\FinanceYahooFactory;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

final class FinanceYahooFacadeTest extends TestCase
{
    public function testCrawlStock(): void
    {
        $facade = $this->createFinanceYahooFacade(self::never());

        $siteCrawler = new RootJsonSiteCrawler([
            'name' => new CompanyName(),
        ]);

        $result = $facade->crawlStock([$siteCrawler], ['AMZN']);
        $amazon = $result->get('AMZN');

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

        $facade = $this->createFinanceYahooFacade(self::once());

        $notifyResult = $facade->notify($policy, new CrawlResult([
            $amazon->ticker()->symbol() => $amazon,
            $google->ticker()->symbol() => $google,
        ]));

        self::assertSame(['AMZN'], $notifyResult->symbols());
        self::assertSame($amazon, $notifyResult->companyForSymbol('AMZN'));
        self::assertSame(['high trend to sell'], $notifyResult->policiesForSymbol('AMZN'));
    }

    private function createFinanceYahooFacade(InvocationOrder $channelSendExpected): FinanceYahooFacade
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->expects($channelSendExpected)->method('send');

        return new FinanceYahooFacade(
            new FinanceYahooFactory(
                HttpClient::create(),
                $channel
            )
        );
    }
}
