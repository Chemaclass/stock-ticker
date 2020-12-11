<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

final class CrawlResult
{
    /** @var array<string,Company> */
    private array $companiesGroupBySymbol;

    /**
     * @param array<string,Company> $companiesGroupBySymbol
     */
    public function __construct(array $companiesGroupBySymbol)
    {
        $this->companiesGroupBySymbol = $companiesGroupBySymbol;
    }

    public function has(string $symbol): bool
    {
        return isset($this->companiesGroupBySymbol[$symbol]);
    }

    public function get(string $symbol): Company
    {
        return $this->companiesGroupBySymbol[$symbol];
    }
}
