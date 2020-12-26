<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\QuoteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifierInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;

interface StockTickerFactoryInterface
{
    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): QuoteCrawlerInterface;

    public function createNotifier(NotifierPolicy $policy, ChannelInterface ...$channels): NotifierInterface;

    /**
     * @return SiteCrawlerInterface[]
     */
    public function createSiteCrawlers(int $maxNewsToFetch): array;

    /**
     * @return ChannelInterface[]
     */
    public function createChannels(array $channelNames): array;
}
