<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\Trend;
use PHPUnit\Framework\TestCase;

final class TrendTest extends TestCase
{
    public function test_to_array(): void
    {
        $array = [
            'period' => '0m',
            'strongBuy' => 11,
            'buy' => 12,
            'hold' => 13,
            'sell' => 14,
            'strongSell' => 15,
        ];

        $model = (new Trend())->fromArray($array);

        self::assertEquals($array, $model->toArray());
        self::assertEquals($array['period'], $model->getPeriod());
        self::assertEquals($array['strongBuy'], $model->getStrongBuy());
        self::assertEquals($array['buy'], $model->getBuy());
        self::assertEquals($array['hold'], $model->getHold());
        self::assertEquals($array['sell'], $model->getSell());
        self::assertEquals($array['strongSell'], $model->getStrongSell());
    }
}
