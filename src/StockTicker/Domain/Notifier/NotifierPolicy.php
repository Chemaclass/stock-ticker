<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier;

use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;

final class NotifierPolicy
{
    /** @var array<string, PolicyGroup> */
    private array $groupedBySymbol;

    public function __construct(array $groupedBySymbol)
    {
        $this->groupedBySymbol = $groupedBySymbol;
    }

    /**
     * @return array<string, PolicyGroup>
     */
    public function groupedBySymbol(): array
    {
        return $this->groupedBySymbol;
    }

    public function symbols(): array
    {
        return array_keys($this->groupedBySymbol);
    }
}
