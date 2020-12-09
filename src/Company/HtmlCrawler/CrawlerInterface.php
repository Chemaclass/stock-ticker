<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company\HtmlCrawler;

interface CrawlerInterface
{
    public function crawlHtml(string $html): string;
}
