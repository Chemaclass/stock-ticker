<?php

declare(strict_types=1);

namespace App\Company;

interface CompanySummaryBuilderInterface
{
    public function crawlHtml(string $html): CompanySummaryResult;
}
