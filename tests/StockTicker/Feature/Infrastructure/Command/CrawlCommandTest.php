<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Feature\Infrastructure\Command;

use Chemaclass\StockTicker\Infrastructure\Command\CrawlCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

final class CrawlCommandTest extends TestCase
{
    public function testExistingTicker(): void
    {
        $actual = $this->runCommand('GM --maxNews=0');
        self::assertSame(Command::SUCCESS, $actual);
    }

    public function testNonExistingTicker(): void
    {
        $actual = $this->runCommand('UNKNOWN_TICKER --maxNews=0');
        self::assertSame(Command::FAILURE, $actual);
    }

    private function runCommand(string $inputString): int
    {
        return (new CrawlCommand())->run(
            new StringInput($inputString),
            $this->createMock(OutputInterface::class)
        );
    }
}
