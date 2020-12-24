<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class Company
{
    private Symbol $symbol;

    /**
     * @var array<string, array>
     */
    private array $info;

    /**
     * @psalm-pure
     */
    public static function empty(): self
    {
        return new self(Symbol::empty(), []);
    }

    public function __construct(Symbol $symbol, array $info)
    {
        $this->symbol = $symbol;
        $this->info = $info;
    }

    public function __toString(): string
    {
        return sprintf('Symbol: %s', $this->symbol->toString());
    }

    public function symbol(): Symbol
    {
        return $this->symbol;
    }

    public function info(string $key): ?array
    {
        return $this->info[$key] ?? null;
    }

    public function allInfo(): array
    {
        return $this->info;
    }
}
