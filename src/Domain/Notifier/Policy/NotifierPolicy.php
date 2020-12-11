<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Policy;

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
}
