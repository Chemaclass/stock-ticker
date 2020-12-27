<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;

final class StockTickerFacade
{
    private const DEFAULT_MAX_NEWS_TO_FETCH = 9;

    private StockTickerFactoryInterface $factory;

    public function __construct(StockTickerFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string[] $symbols
     */
    public function crawlStock(
        array $symbols,
        int $maxNewsToFetch = self::DEFAULT_MAX_NEWS_TO_FETCH
    ): CrawlResult {
        $siteCrawlers = $this->factory
            ->createSiteCrawlers($maxNewsToFetch);

        return $this->factory
            ->createCompanyCrawler(...$siteCrawlers)
            ->crawlStock(...$symbols);
    }

    /**
     * @param string[] $channelNames
     */
    public function sendNotifications(
        array $channelNames,
        NotifierPolicy $policy,
        int $maxNewsToFetch = self::DEFAULT_MAX_NEWS_TO_FETCH
    ): NotifyResult {
        $channels = $this->factory
            ->createChannels($channelNames);

        $crawlResult = $this->crawlStock($policy->symbols(), $maxNewsToFetch);

        return $this->factory
            ->createNotifier($policy, ...$channels)
            ->notify($crawlResult);
    }
}
