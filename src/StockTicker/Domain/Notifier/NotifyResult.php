<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier;

use Chemaclass\StockTicker\Domain\WriteModel\Quote;

final class NotifyResult
{
    /** @psalm-var array<string, array{quote: Quote, conditionNames: string[]}> */
    private array $result = [];

    /**
     * @param string[] $conditionNames
     */
    public function add(Quote $quote, array $conditionNames): self
    {
        /** @var string $symbol */
        $symbol = $quote->getSymbol();

        $this->result[$symbol] = [
            'quote' => $quote,
            'conditionNames' => $conditionNames,
        ];

        return $this;
    }

    public function conditionNamesGroupBySymbol(): array
    {
        $conditionNamesBySymbol = [];

        foreach ($this->symbols() as $symbol) {
            $conditionNamesBySymbol[$symbol] = $this->conditionNamesForSymbol($symbol);
        }

        return $conditionNamesBySymbol;
    }

    /**
     * @return string[]
     */
    public function symbols(): array
    {
        return array_keys($this->result);
    }

    /**
     * @return string[]
     */
    public function conditionNamesForSymbol(string $symbol): array
    {
        return $this->result[$symbol]['conditionNames'];
    }

    public function quoteBySymbol(string $symbol): Quote
    {
        return $this->result[$symbol]['quote'] ?? new Quote();
    }

    public function isEmpty(): bool
    {
        return empty($this->result);
    }
}
