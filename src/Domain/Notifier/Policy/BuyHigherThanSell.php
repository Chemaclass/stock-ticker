<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Policy;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

final class BuyHigherThanSell implements PolicyInterface
{
    public function __invoke(Company $company): bool
    {
        $trend = (array) $company->info('trend')->get();

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
