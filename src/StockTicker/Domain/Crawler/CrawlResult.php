<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler;

use Chemaclass\StockTicker\Domain\WriteModel\Quote;

final class CrawlResult
{
    /** @var array<string,Quote> */
    private array $companiesGroupedBySymbol;

    public function __construct(array $companiesGroupedBySymbol)
    {
        $this->companiesGroupedBySymbol = $companiesGroupedBySymbol;
    }

    public function getQuote(string $symbol): Quote
    {
        return $this->companiesGroupedBySymbol[$symbol] ?? new Quote();
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
