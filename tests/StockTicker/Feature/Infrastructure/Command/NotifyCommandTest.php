<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Feature\Infrastructure\Command;

use Chemaclass\StockTicker\Infrastructure\Command\NotifyCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

final class NotifyCommandTest extends TestCase
{
    public function testExistingTicker(): void
    {
        $actual = $this->runCommand('GM --channels=fake --maxNews=1 --maxRepetitions=1 --sleepingTime=0');
        self::assertSame(Command::SUCCESS, $actual);
    }

    public function testNonExistingTicker(): void
    {
        $actual = $this->runCommand('UNKNOWN_TICKER --channels=fake --maxNews=1 --maxRepetitions=1 --sleepingTime=0');
        self::assertSame(Command::SUCCESS, $actual);
    }

    private function runCommand(string $inputString): int
    {
        return (new NotifyCommand())->run(
            new StringInput($inputString),
            $this->createMock(OutputInterface::class)
        );
    }
}
