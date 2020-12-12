<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Policy;

/**
 * @psalm-immutable
 */
final class PolicyGroup
{
    /** @var array<int|string,PolicyConditionInterface> */
    private array $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array<int|string,PolicyConditionInterface>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }
}
