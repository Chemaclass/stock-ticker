<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;

final class StockTickerFacade
{
    private StockTickerFactoryInterface $factory;

    public function __construct(StockTickerFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param list<SiteCrawlerInterface> $siteCrawlers
     * @param list<string> $symbols
     */
    public function crawlStock(array $siteCrawlers, array $symbols): CrawlResult
    {
        return $this->factory
            ->createCompanyCrawler(...$siteCrawlers)
            ->crawlStock(...$symbols);
    }

    public function notify(NotifierPolicy $policy, CrawlResult $companies): NotifyResult
    {
        return $this->factory
            ->createNotifier($policy)
            ->notify($companies);
    }
}
