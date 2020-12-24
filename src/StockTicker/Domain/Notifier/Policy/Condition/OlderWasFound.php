<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Policy\Condition;

use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyConditionInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Company;

final class OlderWasFound implements PolicyConditionInterface
{
    /**
     * @var array<string,string>
     * For example ['TickerSymbol' => 'datetime']
     */
    private static array $cacheOldestBySymbol = [];

    private string $companyKey;

    private string $keyToCompare;

    public function __construct(string $companyKey, string $keyToCompare = 'datetime')
    {
        $this->companyKey = $companyKey;
        $this->keyToCompare = $keyToCompare;
    }

    public function __invoke(Company $company): bool
    {
        $current = $this->findLatestDateTimeFromNews($company);
        $previous = self::$cacheOldestBySymbol[$company->symbol()->toString()] ?? '';

        self::$cacheOldestBySymbol[$company->symbol()->toString()] = $current;

        return $current > $previous;
    }

    private function findLatestDateTimeFromNews(Company $company): string
    {
        $reduced = array_reduce(
            (array) $company->info($this->companyKey),
            function (?array $carry, array $current): array {
                if (null === $carry) {
                    return $current;
                }

                return $carry[$this->keyToCompare] > $current[$this->keyToCompare]
                    ? $carry
                    : $current;
            }
        );

        return $reduced[$this->keyToCompare];
    }
}
