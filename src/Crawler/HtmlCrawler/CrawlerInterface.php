<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\HtmlCrawler;

interface CrawlerInterface
{
    public function crawlHtml(string $html): string;
}
