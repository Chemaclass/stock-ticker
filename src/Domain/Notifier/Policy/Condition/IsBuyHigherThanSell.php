<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Notifier\Policy\Condition;

use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyConditionInterface;
use Chemaclass\TickerNews\Domain\ReadModel\Company;

final class IsBuyHigherThanSell implements PolicyConditionInterface
{
    public function __invoke(Company $company): bool
    {
        $trend = (array) $company->info('trend');

        $strongBuys = $this->mapValues($trend, 'strongBuy');
        $buys = $this->mapValues($trend, 'buy');
        $sells = $this->mapValues($trend, 'sell');
        $strongSells = $this->mapValues($trend, 'strongSell');

        $totalBuys = array_sum(array_merge($strongBuys, $buys));
        $totalSells = array_sum(array_merge($sells, $strongSells));

        return $totalBuys > $totalSells;
    }

    private function mapValues(array $list, string $key): array
    {
        return array_map(
            static fn (array $t): int => (int) $t[$key],
            array_values($list)
        );
    }
}
