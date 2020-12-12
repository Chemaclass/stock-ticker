<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Policy;

/**
 * @psalm-immutable
 */
final class PolicyGroup
{
    /** @var array<string,callable> */
    private array $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array<string,callable>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }
}
