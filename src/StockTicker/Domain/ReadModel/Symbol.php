<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class Symbol
{
    private string $symbol;

    private function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @psalm-pure
     */
    public static function fromString(string $symbol): self
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

    public function toString(): string
    {
        return $this->symbol;
    }
}
