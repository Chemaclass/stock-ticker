<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler;

use Chemaclass\StockTicker\Domain\ReadModel\Company;

final class CrawlResult
{
    /** @var array<string,Company> */
    private array $companiesGroupedBySymbol;

    public function __construct(array $companiesGroupedBySymbol)
    {
        $this->companiesGroupedBySymbol = $companiesGroupedBySymbol;
    }

    public function getCompany(string $symbol): Company
    {
        return $this->companiesGroupedBySymbol[$symbol] ?? Company::empty();
    }

    public function getCompaniesGroupedBySymbol(): array
    {
        return $this->companiesGroupedBySymbol;
    }

    public function isEmpty(): bool
    {
        return empty($this->companiesGroupedBySymbol);
    }
}
