<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier\Policy\Condition;

use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\IsBuyHigherThanSell;
use Chemaclass\StockTicker\Domain\ReadModel\Company;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use PHPUnit\Framework\TestCase;

final class IsBuyBiggerThanSellTest extends TestCase
{
    private const TREND = 'the key for trend';

    public function testInvoke(): void
    {
        $foundMoreNews = new IsBuyHigherThanSell(self::TREND);

        $company = $this->createCompanyWithNews([
            '0m' => [
                'strongBuy' => '1',
                'buy' => '2',
                'sell' => '3',
                'strongSell' => '4',
            ],
            '-1m' => [
                'strongBuy' => '1',
                'buy' => '2',
                'sell' => '3',
                'strongSell' => '4',
            ],
        ]);
        // ((1 + 2) * 2) > ((3 + 4) * 2)
        self::assertFalse($foundMoreNews($company));
    }

    private function createCompanyWithNews(array $trend): Company
    {
        return new Company(
            Symbol::fromString('SYMBOL'),
            [
                self::TREND => $trend,
            ]
        );
    }
}
