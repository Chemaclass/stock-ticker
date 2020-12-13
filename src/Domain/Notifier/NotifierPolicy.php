<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Notifier;

use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;

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
