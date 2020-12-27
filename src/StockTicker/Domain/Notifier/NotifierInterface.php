<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;

interface NotifierInterface
{
    public function notify(CrawlResult $crawlResult): NotifyResult;
}
