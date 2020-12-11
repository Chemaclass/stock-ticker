<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Policy;

/**
 * @psalm-immutable
 */
final class PolicyGroup
{
    /** @var array<string,callable> */
    private array $policies;

    public function __construct(array $policies)
    {
        $this->policies = $policies;
    }

    /**
     * @return array<string,callable>
     */
    public function policies(): array
    {
        return $this->policies;
    }
}
