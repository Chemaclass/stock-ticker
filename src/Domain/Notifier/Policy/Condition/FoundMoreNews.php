<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Policy\Condition;

use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyConditionInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Company;

final class FoundMoreNews implements PolicyConditionInterface
{
    /**
     * @var array<string,string>
     * For example ['TickerSymbol' => 'datetime']
     */
    private static array $cacheOldestDateTimeBySymbol = [];

    private string $companyKey;

    public function __construct(string $companyKey)
    {
        $this->companyKey = $companyKey;
    }

    public function __invoke(Company $company): bool
    {
        $current = $this->findLatestDateTimeFromNews($company);
        $previous = self::$cacheOldestDateTimeBySymbol[$company->symbol()->toString()] ?? '';

        self::$cacheOldestDateTimeBySymbol[$company->symbol()->toString()] = $current;

        return $current > $previous;
    }

    private function findLatestDateTimeFromNews(Company $company): string
    {
        $reduced = array_reduce(
            (array) $company->info($this->companyKey),
            static function (?array $carry, array $current): array {
                if (null === $carry) {
                    return $current;
                }

                return $carry['datetime'] > $current['datetime']
                    ? $carry
                    : $current;
            }
        );

        return $reduced['datetime'];
    }
}
