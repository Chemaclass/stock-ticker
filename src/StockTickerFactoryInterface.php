<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\CompanyCrawler;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Notifier\Notifier;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;

interface StockTickerFactoryInterface
{
    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler;

    public function createNotifier(NotifierPolicy $policy): Notifier;
}
