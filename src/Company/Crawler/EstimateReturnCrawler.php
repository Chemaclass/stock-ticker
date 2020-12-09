<?php

declare(strict_types=1);

namespace App\Company\Crawler;

use Symfony\Component\DomCrawler\Crawler;

final class EstimateReturnCrawler implements CrawlerInterface
{
    public const NAME = 'EstimateReturn';

    public function crawlHtml(string $html): string
    {
        return (new Crawler($html))
            ->filter('#fr-val-mod .IbBox')
            ->last()
            ->text();
    }
}
