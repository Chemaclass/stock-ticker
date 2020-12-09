<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company\HtmlCrawler\Summary;

use Chemaclass\FinanceYahoo\Company\HtmlCrawler\CrawlerInterface;
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
