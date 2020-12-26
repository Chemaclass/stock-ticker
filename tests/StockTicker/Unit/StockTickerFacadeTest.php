<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit;

use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapperInterface;
use Chemaclass\StockTicker\Domain\Crawler\QuoteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifierInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use Chemaclass\StockTicker\StockTickerFacade;
use Chemaclass\StockTicker\StockTickerFactoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class StockTickerFacadeTest extends TestCase
{
    public function testCrawlStock(): void
    {
        $facade = new StockTickerFacade($this->mockFactory());
        $result = $facade->crawlStock(['AMZN']);
        $amazon = $result->getQuote('AMZN');

        self::assertSame('AMZN', $amazon->getSymbol());
        self::assertSame('Amazon.com, Inc.', $amazon->getCompanyName()->getLongName());
    }

    public function testNotify(): void
    {
        $facade = new StockTickerFacade($this->mockFactory());
        $facade->sendNotifications([EmailChannel::class], new NotifierPolicy([]));
    }

    private function mockFactory(): StockTickerFactoryInterface
    {
        $siteCrawler = $this->createMock(SiteCrawlerInterface::class);
        $siteCrawler->method('crawl')->willReturn(new Site([
            'symbol' => 'AMZN',
            'companyName' => ['longName' => 'Amazon.com, Inc.'],
        ]));

        $factory = $this->createMock(StockTickerFactoryInterface::class);
        $factory->method('createCompanyCrawler')
            ->willReturn(new QuoteCrawler(
                $this->createMock(HttpClientInterface::class),
                $this->mockCrawledInfoMapper(),
                $siteCrawler
            ));

        $channel = $this->createMock(ChannelInterface::class);
        $channel->expects(self::never())->method('send');
        $factory->method('createChannels')->willReturn([$channel]);

        $notifier = $this->createMock(NotifierInterface::class);
        $notifier->method('notify')->willReturn(new NotifyResult());
        $factory->method('createNotifier')->willReturn($notifier);

        return $factory;
    }

    private function mockCrawledInfoMapper()
    {
        $crawledInfoMapper = $this->createMock(CrawledInfoMapperInterface::class);
        $crawledInfoMapper
            ->method('mapQuote')
            ->willReturnCallback(fn (array $info): Quote => (new Quote())->fromArray($info));

        return $crawledInfoMapper;
    }
}
