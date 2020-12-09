<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company\ReadModel;

/** @psalm-immutable */
final class Company
{
    private array $summary;

    public function __construct(array $summary)
    {
        $this->summary = $summary;
    }

    public function summary(): array
    {
        return $this->summary;
    }
}
