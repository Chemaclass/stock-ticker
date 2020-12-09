<?php

declare(strict_types=1);

namespace App\Company\HtmlCrawler;

interface CrawlerInterface
{
    public function crawlHtml(string $html): string;
}
