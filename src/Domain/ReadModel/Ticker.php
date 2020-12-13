<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class Ticker
{
    private string $symbol;

    /**
     * @psalm-pure
     */
    public static function withSymbol(string $symbol): self
    {
        return new self($symbol);
    }

    /**
     * @psalm-pure
     */
    public static function empty(): self
    {
        return new self('');
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
