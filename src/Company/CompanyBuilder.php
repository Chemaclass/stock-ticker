<?php

declare(strict_types=1);

namespace App\Company;

use App\Company\ReadModel\Company;
use App\Company\ReadModel\Summary;

final class CompanyBuilder
{
    /** @var SummaryCrawlerInterface[] */
    private array $summaryCrawlerInterfaces;

    public function __construct(SummaryCrawlerInterface ...$summaryCrawlerInterfaces)
    {
        $this->summaryCrawlerInterfaces = $summaryCrawlerInterfaces;
    }

    public function buildFromHtml(string $html): Company
    {
        $summary = $this->crawlSummary($html);

        return new Company($summary);
    }

    private function crawlSummary(string $html): Summary
    {
        $summary = [];

        foreach ($this->summaryCrawlerInterfaces as $crawler) {
            $result = $crawler->crawlHtml($html);
            $summary[$result->key()] = $result->value();
        }

        return new Summary($summary);
    }
}
