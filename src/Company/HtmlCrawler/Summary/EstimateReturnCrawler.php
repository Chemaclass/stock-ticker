<?php

declare(strict_types=1);

namespace App\Company\HtmlCrawler\Summary;

use App\Company\HtmlCrawler\CrawlerInterface;
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
