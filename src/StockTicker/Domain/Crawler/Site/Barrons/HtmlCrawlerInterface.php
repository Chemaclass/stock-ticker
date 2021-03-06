<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\Barrons;

interface HtmlCrawlerInterface
{
    public function crawlHtml(string $html): array;
}
