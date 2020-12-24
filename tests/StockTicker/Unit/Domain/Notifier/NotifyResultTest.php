<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier;

use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\ReadModel\Company;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use PHPUnit\Framework\TestCase;

final class NotifyResultTest extends TestCase
{
    public function testConditionNamesGroupBySymbol(): void
    {
        $notifyResult = (new NotifyResult())
            ->add($this->createCompany('SYMBOL_1'), ['condition 1', 'condition 2'])
            ->add($this->createCompany('SYMBOL_2'), ['condition 3']);

        self::assertEquals(
            [
                'SYMBOL_1' => ['condition 1', 'condition 2'],
                'SYMBOL_2' => ['condition 3'],
            ],
            $notifyResult->conditionNamesGroupBySymbol()
        );
    }

    private function createCompany(string $symbol): Company
    {
        return new Company(
            Symbol::fromString($symbol),
            ['key1' => 'value 1']
        );
    }
}
