<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\Analysis;

use Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\CrawlerInterface;
use Symfony\Component\DomCrawler\Crawler;

final class EarningsEstimateCrawler implements CrawlerInterface
{
    public function crawlHtml(string $html): string
    {
        $text = (new Crawler($html))
            ->filter('table:nth-child(2) tbody tr:nth-child(1) td:nth-child(2)')
            ->last()
            ->text();

        return 'No. of Analysts: ' . $text;
    }
}
