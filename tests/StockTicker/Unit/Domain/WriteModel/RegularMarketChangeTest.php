<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\RegularMarketChange;
use PHPUnit\Framework\TestCase;

final class RegularMarketChangeTest extends TestCase
{
    public function testToArray(): void
    {
        $array = [
            'raw' => 10.989,
            'fmt' => '10.99',
        ];

        $model = (new RegularMarketChange())->fromArray($array);

        self::assertEquals($array, $model->toArray());
        self::assertEquals($array['raw'], $model->getRaw());
        self::assertEquals($array['fmt'], $model->getFmt());
    }
}
