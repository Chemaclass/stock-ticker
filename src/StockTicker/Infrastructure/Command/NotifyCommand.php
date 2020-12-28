<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Infrastructure\Command;

use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Fake\FakeChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\RecentNewsWasFound;
use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\StockTicker\Infrastructure\Command\ReadModel\NotifyCommandInput;
use Chemaclass\StockTicker\StockTickerConfig;
use Chemaclass\StockTicker\StockTickerFacade;
use Chemaclass\StockTicker\StockTickerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NotifyCommand extends Command
{
    private const DEFAULT_MAX_NEWS_TO_FETCH = 3;
    private const DEFAULT_MAX_REPETITIONS = PHP_INT_MAX;
    private const DEFAULT_SLEEPING_TIME_IN_SECONDS = 60;
    private const DEFAULT_CHANNEL = 'email';

    private const MAP_POSSIBLE_CHANNELS = [
        'email' => EmailChannel::class,
        'slack' => SlackChannel::class,
        'fake' => FakeChannel::class,
    ];

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private OutputInterface $output;

    protected function configure(): void
    {
        $this
            ->setName('notify')
            ->setDescription('It notifies based on conditions applied to the crawled result')
            ->addArgument(
                'symbols',
                InputArgument::IS_ARRAY|InputArgument::REQUIRED,
                'Which stock symbols do you want to crawl?'
            )
            ->addOption(
                'channels',
                'c',
                InputArgument::OPTIONAL,
                'Channels to notify separated by comma [email,slack]',
                self::DEFAULT_CHANNEL
            )
            ->addOption(
                'maxNews',
                'm',
                InputArgument::OPTIONAL,
                'Max number of news to fetch per Quote',
                self::DEFAULT_MAX_NEWS_TO_FETCH
            )
            ->addOption(
                'maxRepetitions',
                'r',
                InputArgument::OPTIONAL,
                'Max number repetitions for the loop',
                self::DEFAULT_MAX_REPETITIONS
            )
            ->addOption(
                'sleepingTime',
                's',
                InputArgument::OPTIONAL,
                'Sleeping time in seconds',
                self::DEFAULT_SLEEPING_TIME_IN_SECONDS
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $commandInput = NotifyCommandInput::createFromInput($input);
        $channelNames = $this->mapChannelNames($commandInput->getChannelsAsString());

        $policy = $this->createPolicyForSymbols($commandInput->getSymbols());
        $facade = $this->createStockTickerFacade();

        for ($i = 0; $i < $commandInput->getMaxRepetitions(); $i++) {
            $this->printStartingIteration($commandInput, $i);

            $notifyResult = $facade->sendNotifications(
                $channelNames,
                $policy,
                $commandInput->getMaxNews()
            );

            $this->printNotifyResult($notifyResult);
            $this->sleepWithPrompt($commandInput->getSleepingTime());
        }

        $output->writeln('Already reached the max repetition limit of ' . $commandInput->getMaxRepetitions());

        return Command::SUCCESS;
    }

    private function mapChannelNames(string $channelsAsString): array
    {
        return array_filter(array_map(
            static fn (string $c): string => self::MAP_POSSIBLE_CHANNELS[$c] ?? '',
            explode(',', $channelsAsString)
        ));
    }

    /**
     * Create the same policy group for all symbols.
     *
     * @param string[] $symbols
     */
    private function createPolicyForSymbols(array $symbols): NotifierPolicy
    {
        $conditions = array_fill_keys($symbols, new PolicyGroup([
            'Recent news was found' => new RecentNewsWasFound(),
        ]));

        return new NotifierPolicy($conditions);
    }

    private function createStockTickerFacade(): StockTickerFacade
    {
        return new StockTickerFacade(
            new StockTickerFactory(
                new StockTickerConfig(
                    dirname(__DIR__) . '/../Presentation/notification',
                    $_ENV
                )
            ),
        );
    }

    private function printStartingIteration(NotifyCommandInput $commandInput, int $actualIteration): void
    {
        $this->output->writeln(sprintf('Looking for news in %s ...', implode(', ', $commandInput->getSymbols())));
        $this->output->writeln(sprintf('Completed %d of %d', $actualIteration, $commandInput->getMaxRepetitions()));
    }

    private function printNotifyResult(NotifyResult $notifyResult): void
    {
        if ($notifyResult->isEmpty()) {
            $this->output->writeln(' ~~~~ Nothing new here ~~~~');

            return;
        }

        $this->output->writeln('===========================');
        $this->output->writeln('====== Notify result ======');
        $this->output->writeln('===========================');

        foreach ($notifyResult->conditionNamesGroupBySymbol() as $symbol => $conditionNames) {
            $quote = $notifyResult->quoteBySymbol($symbol);

            $companyName = (null !== $quote->getCompanyName())
                ? $quote->getCompanyName()->getLongName() ?? ''
                : '';

            $symbol = $quote->getSymbol() ?? '';
            $this->output->writeln(sprintf('%s (%s)', $companyName, $symbol));

            foreach ($conditionNames as $conditionName) {
                $this->output->writeln("  - $conditionName");
            }
            $this->output->writeln('');
        }
        $this->output->writeln('');
    }

    private function sleepWithPrompt(int $sec): void
    {
        $this->output->writeln("Sleeping {$sec} seconds...");
        $len = mb_strlen((string) $sec);

        for ($i = $sec; $i > 0; $i--) {
            $this->output->write(sprintf("%0{$len}d\r", $i));
            sleep(1);
        }

        $this->output->writeln('Awake again!');
    }
}
