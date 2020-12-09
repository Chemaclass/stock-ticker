<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\Summary;

use Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\CrawlerInterface;
use Symfony\Component\DomCrawler\Crawler;

final class EstimateReturnCrawler implements CrawlerInterface
{
    public function crawlHtml(string $html): string
    {
        return (new Crawler($html))
            ->filter('#fr-val-mod .IbBox')
            ->last()
            ->text();
    }
}
