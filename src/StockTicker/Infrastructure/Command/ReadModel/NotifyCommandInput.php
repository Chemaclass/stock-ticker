<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Infrastructure\Command\ReadModel;

use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Fake\FakeChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Symfony\Component\Console\Input\InputInterface;

final class NotifyCommandInput
{
    private const MAP_POSSIBLE_CHANNELS = [
        'email' => EmailChannel::class,
        'slack' => SlackChannel::class,
        'fake' => FakeChannel::class,
    ];

    private array $symbols;
    private int $maxNews;
    private int $maxRepetitions;
    private int $sleepingTime;
    private array $channelNames;

    public static function createFromInput(InputInterface $input): self
    {
        /** @psalm-suppress PossiblyInvalidCast */
        $channelsAsString = (string) $input->getOption('channels');

        return new self(
            (array) $input->getArgument('symbols'),
            (int) $input->getOption('maxNews'),
            (int) $input->getOption('maxRepetitions'),
            (int) $input->getOption('sleepingTime'),
            self::mapChannelNames($channelsAsString)
        );
    }

    public function __construct(
        array $symbols,
        int $maxNews,
        int $maxRepetitions,
        int $sleepingTime,
        array $channelNames
    ) {
        $this->symbols = $symbols;
        $this->maxNews = $maxNews;
        $this->maxRepetitions = $maxRepetitions;
        $this->sleepingTime = $sleepingTime;
        $this->channelNames = $channelNames;
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

    public function getChannelNames(): array
    {
        return $this->channelNames;
    }

    private static function mapChannelNames(string $channelsAsString): array
    {
        return array_filter(array_map(
            static fn (string $c): string => self::MAP_POSSIBLE_CHANNELS[$c] ?? '',
            explode(',', $channelsAsString)
        ));
    }
}
