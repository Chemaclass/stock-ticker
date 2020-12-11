<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Ticker;
use JsonException;

final class FinanceYahooConfig implements FinanceYahooConfigInterface
{
    private string $tickers;

    public function __construct(string $tickers)
    {
        $this->tickers = $tickers;
    }

    /**
     * @throws JsonException
     *
     * @return Ticker[]
     */
    public function getTickers(): array
    {
        return array_map(
            static fn (string $symbol) => Ticker::withSymbol($symbol),
            (array) json_decode($this->tickers, true, 512, JSON_THROW_ON_ERROR)
        );
    }
}
