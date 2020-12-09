<?php

declare(strict_types=1);

namespace App\Company;

use App\Company\ReadModel\Company;
use App\Company\ReadModel\Summary;

final class CompanyBuilder
{
    /** @var CompanySummaryBuilderInterface[] */
    private array $companySummaryBuilderInterfaces;

    public function __construct(
        CompanySummaryBuilderInterface ...$companyBuilderPieceInterfaces
    ) {
        $this->companySummaryBuilderInterfaces = $companyBuilderPieceInterfaces;
    }

    public function buildFromHtml(string $html): Company
    {
        $summary = $this->buildSummary($html);

        return new Company($summary);
    }

    private function buildSummary(string $html): Summary
    {
        $summary = [];

        foreach ($this->companySummaryBuilderInterfaces as $summaryBuilder) {
            $result = $summaryBuilder->crawlHtml($html);
            $summary[$result->key()] = $result->value();
        }

        return new Summary($summary);
    }
}
