<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\NewsNotifier;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;

interface StockTickerFactoryInterface
{
    public function createNewsNotifier(ChannelInterface ...$channels): NewsNotifier;

    /**
     * @return SiteCrawlerInterface[]
     */
    public function createSiteCrawlers(int $maxNewsToFetch): array;

    /**
     * @return ChannelInterface[]
     */
    public function createChannels(array $channelNames): array;
}
