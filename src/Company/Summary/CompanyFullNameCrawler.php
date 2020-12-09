<?php

declare(strict_types=1);

namespace App\Company\Summary;

use App\Company\CompanySummaryResult;
use App\Company\SummaryCrawlerInterface;
use Symfony\Component\DomCrawler\Crawler;

final class CompanyFullNameCrawler implements SummaryCrawlerInterface
{
    public const NAME = 'CompanyFullName';

    public function crawlHtml(string $html): CompanySummaryResult
    {
        $text = (new Crawler($html))
            ->filter('meta[name="description"]')
            ->attr('content') ?? '';

        preg_match('/^Find the latest (?<name>.*) stock quote/', $text, $matches);

        return new CompanySummaryResult(self::NAME, $matches['name']);
    }
}
