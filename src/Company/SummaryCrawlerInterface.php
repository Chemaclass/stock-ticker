<?php

declare(strict_types=1);

namespace App\Company;

interface SummaryCrawlerInterface
{
    public function crawlHtml(string $html): CompanySummaryResult;
}
