<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Notifier;

use Chemaclass\TickerNews\Domain\ReadModel\Company;

final class NotifyResult
{
    /** @psalm-var array<string, array{company: Company, conditionNames: string[]}> */
    private array $result = [];

    /**
     * @param string[] $conditionNames
     */
    public function add(Company $company, array $conditionNames): void
    {
        $this->result[$company->ticker()->symbol()] = [
            'company' => $company,
            'conditionNames' => $conditionNames,
        ];
    }

    /**
     * @return string[]
     */
    public function symbols(): array
    {
        return array_keys($this->result);
    }

    /**
     * @return string[]
     */
    public function conditionNamesForSymbol(string $symbol): array
    {
        return $this->result[$symbol]['conditionNames'];
    }

    public function conditionNamesGroupBySymbol(): array
    {
        $conditionNamesBySymbol = [];

        foreach ($this->symbols() as $symbol) {
            $conditionNamesBySymbol[$symbol] = $this->conditionNamesForSymbol($symbol);
        }

        return $conditionNamesBySymbol;
    }

    public function companyForSymbol(string $symbol): Company
    {
        return $this->result[$symbol]['company'] ?? Company::empty();
    }

    public function isEmpty(): bool
    {
        return empty($this->result);
    }
}
