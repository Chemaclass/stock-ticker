<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Gacela\Framework\AbstractFacade;

/**
 * @method StockTickerFactory getFactory()
 */
final class StockTickerFacade extends AbstractFacade
{
    private const DEFAULT_MAX_NEWS_TO_FETCH = 9;

    /**
     * @param string[] $channelNames
     */
    public function sendNotifications(
        array $channelNames,
        NotifierPolicy $policy,
        int $maxNewsToFetch = self::DEFAULT_MAX_NEWS_TO_FETCH,
    ): NotifyResult {
        $channels = $this->getFactory()
            ->createChannels($channelNames);

        $crawlResult = $this->crawlStock($policy->symbols(), $maxNewsToFetch);

        return $this->getFactory()
            ->createNotifier($policy, ...$channels)
            ->notify($crawlResult);
    }

    /**
     * @param string[] $symbols
     */
    public function crawlStock(
        array $symbols,
        int $maxNewsToFetch = self::DEFAULT_MAX_NEWS_TO_FETCH,
    ): CrawlResult {
        $siteCrawlers = $this->getFactory()
            ->createSiteCrawlers($maxNewsToFetch);

        return $this->getFactory()
            ->createQuoteCrawler(...$siteCrawlers)
            ->crawlStock(...$symbols);
    }
}
