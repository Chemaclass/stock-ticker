<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier\Policy\Condition;

use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\RecentNewsWasFound;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use PHPUnit\Framework\TestCase;

final class RecentNewsWasFoundTest extends TestCase
{
    private const NEWS = 'the key where the news are';

    public function testInvoke(): void
    {
        $foundMoreNews = new RecentNewsWasFound();

        $company = $this->createCompanyWithNews([
            [
                'title' => 'the first article will be consider new',
                'datetime' => '2020-6-15 02:00',
            ],
            [
                'title' => 'another article on the first round, but with an older datetime',
                'datetime' => '2020-1-01 00:00',
            ],
        ]);
        self::assertTrue($foundMoreNews($company));

        $company = $this->createCompanyWithNews([
            [
                'title' => 'it has an older datetime, so it wont be consider new',
                'datetime' => '2020-2-19 01:00',
            ],
        ]);
        self::assertFalse($foundMoreNews($company));

        $company = $this->createCompanyWithNews([
            [
                'title' => 'it has an older date than the first article, so it is consider new',
                'datetime' => '2020-7-18 03:00',
            ],
        ]);
        self::assertTrue($foundMoreNews($company));
    }

    private function createCompanyWithNews(array $news): Quote
    {
        return (new Quote())
            ->setSymbol('SYMBOL')
            ->setLatestNews($news);
    }
}
