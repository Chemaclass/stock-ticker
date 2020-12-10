<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\ReadModel;

/** @psalm-immutable */
final class Ticker
{
    private string $symbol;

    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    public function symbol(): string
    {
        return $this->symbol;
    }
}
