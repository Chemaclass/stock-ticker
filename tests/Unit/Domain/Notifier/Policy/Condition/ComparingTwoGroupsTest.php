<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier\Policy\Condition;

use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\ComparingTwoGroups;
use Chemaclass\StockTicker\Domain\ReadModel\Company;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use PHPUnit\Framework\TestCase;

final class ComparingTwoGroupsTest extends TestCase
{
    private const KEY = 'the key where the groups are';

    public function testInvoke(): void
    {
        $foundMoreNews = new ComparingTwoGroups(
            self::KEY,
            ['a', 'b'],
            ['c', 'd'],
        );

        $company = $this->createCompanyWithNews([
            '0m' => [
                'a' => '1',
                'b' => '2',
                'c' => '3',
                'd' => '4',
            ],
            '-1m' => [
                'a' => '1',
                'b' => '2',
                'c' => '3',
                'd' => '4',
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
                self::KEY => $trend,
            ]
        );
    }
}
