<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\E2E;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\CompanyNameExtractor;
use Chemaclass\FinanceYahoo\Domain\Crawler\RootAppJsonCrawler;
use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\NotifierPolicy;
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

        $siteCrawler = new RootAppJsonCrawler([
            'name' => new CompanyNameExtractor(),
        ]);

        $companies = $facade->crawlStock([$siteCrawler], ['AMZN']);

        $first = reset($companies);
        self::assertEquals(Ticker::withSymbol('AMZN'), $first->ticker());
        self::assertEquals('Amazon.com, Inc.', $first->summary()['name']);
    }

    public function testNotify(): void
    {
        $facade = $this->createFinanceYahooFacade(self::exactly(2));

        $companies = [
            'AMZN' => new Company(
                Ticker::withSymbol('AMZN'),
                ['trend' => ExtractedFromJson::fromArray(['buy' => 0, 'sell' => 10])],
            ),
            'GOOG' => new Company(
                Ticker::withSymbol('GOOG'),
                ['trend' => ExtractedFromJson::fromArray(['buy' => 10, 'sell' => 0])],
            ),
        ];

        $policyGroup = new PolicyGroup([
            'high trend to buy' => static fn (Company $c) => $c->summary()['trend']->asArray()['buy'] > 5,
            'high trend to sell' => static fn (Company $c) => $c->summary()['trend']->asArray()['sell'] > 5,
        ]);

        $policy = new NotifierPolicy([
            'AMZN' => $policyGroup,
            'GOOG' => $policyGroup,
        ]);

        $notifyResult = $facade->notify($policy, $companies);

        self::assertEquals([
            'AMZN' => 'high trend to sell',
            'GOOG' => 'high trend to buy',
        ], $notifyResult->asArray());
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
