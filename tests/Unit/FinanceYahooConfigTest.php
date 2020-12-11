<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Ticker;
use Chemaclass\FinanceYahoo\FinanceYahooConfig;
use JsonException;
use PHPUnit\Framework\TestCase;

final class FinanceYahooConfigTest extends TestCase
{
    /** @test */
    public function exceptionIfJsonMalformed(): void
    {
        $this->expectException(JsonException::class);
        $config = new FinanceYahooConfig('["malformed json');
        $config->getTickers();
    }

    /** @test */
    public function getTickers(): void
    {
        $config = new FinanceYahooConfig('["AAA","BBB"]');

        self::assertEquals([
            Ticker::withSymbol('AAA'),
            Ticker::withSymbol('BBB'),
        ], $config->getTickers());
    }
}
