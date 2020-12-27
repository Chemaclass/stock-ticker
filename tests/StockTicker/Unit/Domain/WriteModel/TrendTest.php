<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\Trend;
use PHPUnit\Framework\TestCase;

final class TrendTest extends TestCase
{
    public function testToArray(): void
    {
        $trend = (new Trend())
            ->setPeriod('0m')
            ->setStrongBuy(1)
            ->setBuy(2)
            ->setHold(3)
            ->setSell(4)
            ->setStrongSell(5);

        self::assertEquals([
            'period' => '0m',
            'strongBuy' => 1,
            'buy' => 2,
            'hold' => 3,
            'sell' => 4,
            'strongSell' => 5,
        ], $trend->toArray());
    }
}
