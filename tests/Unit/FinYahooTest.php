<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\FinYahoo;
use PHPUnit\Framework\TestCase;

final class FinYahooTest extends TestCase
{
    /** @test */
    public function itCanSum(): void
    {
        self::assertSame(0, FinYahoo::sum());
        self::assertSame(1, FinYahoo::sum(1));
        self::assertSame(3, FinYahoo::sum(1, 2));
        self::assertSame(6, FinYahoo::sum(1, 2, 3));
        self::assertSame(10, FinYahoo::sum(1, 2, 3, 4));
    }
}
