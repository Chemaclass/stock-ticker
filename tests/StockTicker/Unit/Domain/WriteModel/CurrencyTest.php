<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\Currency;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    public function testToArray(): void
    {
        $array = [
            'currency' => 'USD',
            'symbol' => '$',
        ];

        $model = (new Currency())->fromArray($array);

        self::assertEquals($array, $model->toArray());
        self::assertEquals($array['currency'], $model->getCurrency());
        self::assertEquals($array['symbol'], $model->getSymbol());
    }
}
