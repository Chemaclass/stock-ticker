<?php

declare(strict_types=1);

namespace App\Company;

/** @psalm-immutable */
final class CompanySummaryResult
{
    private string $key;

    private string $value;

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function value(): string
    {
        return $this->value;
    }
}
