<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class Ticker
{
    private string $symbol;

    public static function withSymbol(string $symbol): self
    {
        return new self($symbol);
    }

    private function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    public function symbol(): string
    {
        return $this->symbol;
    }
}
