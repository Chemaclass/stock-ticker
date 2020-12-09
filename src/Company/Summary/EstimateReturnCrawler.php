<?php

declare(strict_types=1);

namespace App\Company\Summary;

use App\Company\CompanySummaryResult;
use App\Company\SummaryCrawlerInterface;
use Symfony\Component\DomCrawler\Crawler;

final class EstimateReturnCrawler implements SummaryCrawlerInterface
{
    public const NAME = 'EstimateReturn';

    public function crawlHtml(string $html): CompanySummaryResult
    {
        $text = (new Crawler($html))
            ->filter('#fr-val-mod .IbBox')
            ->last()
            ->text();

        return new CompanySummaryResult(self::NAME, $text);
    }
}
