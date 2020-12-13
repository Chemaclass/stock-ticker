<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\Barrons;

interface HtmlCrawlerInterface
{
    public function crawlHtml(string $html): array;
}
