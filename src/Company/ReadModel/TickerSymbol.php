<?php

declare(strict_types=1);

namespace App\Company\ReadModel;

/** @psalm-immutable */
final class TickerSymbol
{
    private string $ticker;

    public function __construct(string $ticker)
    {
        $this->ticker = $ticker;
    }

    public function toString(): string
    {
        return $this->ticker;
    }
}
