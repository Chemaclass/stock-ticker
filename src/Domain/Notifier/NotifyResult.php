<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

final class NotifyResult
{
    /** @psalm-var array<string, array{company: Company, policies: string[]}> */
    private array $result = [];

    /**
     * @param string[] $policyNames
     */
    public function add(Company $company, array $policyNames): void
    {
        $this->result[$company->ticker()->symbol()] = [
            'company' => $company,
            'policies' => $policyNames,
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
    public function policiesForSymbol(string $symbol): array
    {
        return $this->result[$symbol]['policies'];
    }

    public function policiesGroupBySymbol(): array
    {
        $policiesBySymbol = [];

        foreach ($this->symbols() as $symbol) {
            $policiesBySymbol[$symbol] = $this->policiesForSymbol($symbol);
        }

        return $policiesBySymbol;
    }

    public function companyForSymbol(string $symbol): Company
    {
        return $this->result[$symbol]['company'];
    }
}
