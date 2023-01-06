<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\RegularMarketChangePercent;
use PHPUnit\Framework\TestCase;

final class RegularMarketChangePercentTest extends TestCase
{
    public function test_to_array(): void
    {
        $array = [
            'raw' => 10.989,
            'fmt' => '10.99',
        ];

        $model = (new RegularMarketChangePercent())->fromArray($array);

        self::assertEquals($array, $model->toArray());
        self::assertEquals($array['raw'], $model->getRaw());
        self::assertEquals($array['fmt'], $model->getFmt());
    }
}
