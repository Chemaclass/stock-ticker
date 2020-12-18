<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Policy\Condition;

use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyConditionInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Company;

final class ComparingTwoGroups implements PolicyConditionInterface
{
    private string $companyKey;

    private array $groupA;

    private array $groupB;

    public function __construct(string $companyKey, array $groupA, array $groupB)
    {
        $this->companyKey = $companyKey;
        $this->groupA = $groupA;
        $this->groupB = $groupB;
    }

    public function __invoke(Company $company): bool
    {
        $trend = (array) $company->info($this->companyKey);
        $valuesMapper = fn (string $i): array => $this->mapValues($trend, $i);
        $valuesA = array_map($valuesMapper, $this->groupA);
        $valuesB = array_map($valuesMapper, $this->groupB);

        return array_sum($valuesA) > array_sum($valuesB);
    }

    private function mapValues(array $list, string $key): array
    {
        return array_map(
            static fn (array $t): int => (int) $t[$key],
            array_values($list)
        );
    }
}
