<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\CompanyName;
use PHPUnit\Framework\TestCase;

final class CompanyNameTest extends TestCase
{
    public function testToArray(): void
    {
        $array = [
            'shortName' => 'Short Company name, Inc.',
            'longName' => 'Long Company name, Inc.',
        ];

        $model = (new CompanyName())->fromArray($array);

        self::assertEquals($array, $model->toArray());
        self::assertEquals($array['shortName'], $model->getShortName());
        self::assertEquals($array['longName'], $model->getLongName());
    }
}
