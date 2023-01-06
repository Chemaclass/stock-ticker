<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\MarketCap;
use PHPUnit\Framework\TestCase;

final class MarketCapTest extends TestCase
{
    public function test_to_array(): void
    {
        $array = [
            'raw' => 10.989,
            'fmt' => '10.99',
            'longFmt' => '$',
        ];

        $model = (new MarketCap())->fromArray($array);

        self::assertEquals($array, $model->toArray());
        self::assertEquals($array['raw'], $model->getRaw());
        self::assertEquals($array['fmt'], $model->getFmt());
        self::assertEquals($array['longFmt'], $model->getLongFmt());
    }
}
