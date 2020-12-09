<?php

declare(strict_types=1);

namespace App\Company;

use App\Company\Crawler\CrawlerInterface;
use App\Company\ReadModel\Company;

final class CompanyBuilder
{
    /** @var array<string, CrawlerInterface> */
    private array $crawlerInterfaces;

    /**
     * @param array<string, CrawlerInterface> $crawlerInterfaces
     */
    public function __construct(array $crawlerInterfaces)
    {
        $this->crawlerInterfaces = $crawlerInterfaces;
    }

    public function buildFromHtml(string $html): Company
    {
        $summary = [];

        foreach ($this->crawlerInterfaces as $name => $crawler) {
            $summary[$name] = $crawler->crawlHtml($html);
        }

        return new Company($summary);
    }
}
