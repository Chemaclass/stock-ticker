<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier\Policy\Condition;

use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\RecentNewsWasFound;
use Chemaclass\StockTicker\Domain\WriteModel\News;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use PHPUnit\Framework\TestCase;

final class RecentNewsWasFoundTest extends TestCase
{
    public function test_invoke(): void
    {
        $foundMoreNews = new RecentNewsWasFound();

        $company = $this->createCompanyWithNews([
            (new News())
                ->setTitle('the first article will be consider new')
                ->setDatetime('2020-10-02 02:00'),
            (new News())
                ->setTitle('another article on the first round, but with an older datetime')
                ->setDatetime('2019-01-01 00:00'),
        ]);
        self::assertTrue($foundMoreNews($company));

        $company = $this->createCompanyWithNews([
            (new News())
                ->setTitle('it has an older datetime, so it wont be consider new')
                ->setDatetime('2020-10-01 02:00'),
        ]);
        self::assertFalse($foundMoreNews($company));

        $company = $this->createCompanyWithNews([
            (new News())
                ->setTitle('it has an latest datetime, so it will be consider as new')
                ->setDatetime('2020-10-03 02:00'),
        ]);
        self::assertTrue($foundMoreNews($company));

        $company = (new Quote())
            ->setLatestNews([
                (new News())
                    ->setTitle('it has an latest datetime but no symbol in this quote, so it wont be processed')
                    ->setDatetime('2020-10-04 02:00'),
            ]);
        self::assertFalse($foundMoreNews($company));
    }

    private function createCompanyWithNews(array $news): Quote
    {
        return (new Quote())
            ->setSymbol('SYMBOL')
            ->setLatestNews($news);
    }
}
