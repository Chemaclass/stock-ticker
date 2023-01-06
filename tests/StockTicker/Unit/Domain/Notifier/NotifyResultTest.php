<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier;

use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use PHPUnit\Framework\TestCase;

final class NotifyResultTest extends TestCase
{
    public function test_condition_names_group_by_symbol(): void
    {
        $notifyResult = (new NotifyResult())
            ->add($this->createCompany('SYMBOL_1'), ['condition 1', 'condition 2'])
            ->add($this->createCompany('SYMBOL_2'), ['condition 3']);

        self::assertEquals(
            [
                'SYMBOL_1' => ['condition 1', 'condition 2'],
                'SYMBOL_2' => ['condition 3'],
            ],
            $notifyResult->conditionNamesGroupBySymbol(),
        );
    }

    private function createCompany(string $symbol): Quote
    {
        return (new Quote())
            ->setSymbol($symbol);
    }
}
