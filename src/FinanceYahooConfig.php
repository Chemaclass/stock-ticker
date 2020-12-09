<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Crawler\ReadModel\Ticker;
use JsonException;

final class FinanceYahooConfig implements FinanceYahooConfigInterface
{
    /**
     * @return Ticker[]
     */
    public function getTickers(): array
    {
        try {
            return array_map(
                static fn (string $symbol) => new Ticker($symbol),
                json_decode($_ENV['TICKERS'], true, 512, JSON_THROW_ON_ERROR)
            );
        } catch (JsonException $e) {
            die($e->getMessage());
        }
    }
}
