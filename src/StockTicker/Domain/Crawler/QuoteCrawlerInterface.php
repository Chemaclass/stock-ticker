<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler;

interface QuoteCrawlerInterface
{
    public function crawlStock(string ...$symbolStrings): CrawlResult;
}
