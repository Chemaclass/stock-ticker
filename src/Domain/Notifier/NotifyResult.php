<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

final class NotifyResult
{
    private array $result = [];

    public function add(Company $company, string $policyName): void
    {
        $this->result[$company->ticker()->symbol()] = $policyName;
    }

    public function asArray(): array
    {
        return $this->result;
    }
}
