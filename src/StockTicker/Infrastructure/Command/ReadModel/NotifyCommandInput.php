<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Infrastructure\Command\ReadModel;

use Symfony\Component\Console\Input\InputInterface;

final class NotifyCommandInput
{
    /** @var list<string> */
    private array $symbols;
    private int $maxNews;
    private int $maxRepetitions;
    private int $sleepingTime;
    private string $channelsAsString;

    public static function createFromInput(InputInterface $input): self
    {
        /** @psalm-suppress PossiblyInvalidCast */
        $channelsAsString = (string) $input->getOption('channels');

        return new self(
            (array) $input->getArgument('symbols'),
            (int) $input->getOption('maxNews'),
            (int) $input->getOption('maxRepetitions'),
            (int) $input->getOption('sleepingTime'),
            $channelsAsString
        );
    }

    /** @param list<string> $symbols*/
    public function __construct(
        array $symbols,
        int $maxNews,
        int $maxRepetitions,
        int $sleepingTime,
        string $channelsAsString
    ) {
        $this->symbols = $symbols;
        $this->maxNews = $maxNews;
        $this->maxRepetitions = $maxRepetitions;
        $this->sleepingTime = $sleepingTime;
        $this->channelsAsString = $channelsAsString;
    }

    /**
     * @return string[] $symbols
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    public function getMaxNews(): int
    {
        return $this->maxNews;
    }

    public function getMaxRepetitions(): int
    {
        return $this->maxRepetitions;
    }

    public function getSleepingTime(): int
    {
        return $this->sleepingTime;
    }

    public function getChannelsAsString(): string
    {
        return $this->channelsAsString;
    }
}
