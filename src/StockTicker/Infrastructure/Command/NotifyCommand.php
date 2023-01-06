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
use Chemaclass\StockTicker\StockTickerFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class NotifyCommand extends Command
{
    private const DEFAULT_MAX_NEWS_PER_CRAWLED_SITE = 3;
    private const DEFAULT_MAX_REPETITIONS = PHP_INT_MAX;
    private const DEFAULT_SLEEPING_TIME_IN_SECONDS = 60;
    private const DEFAULT_CHANNEL = 'email';

    private const MAP_POSSIBLE_CHANNELS = [
        'email' => EmailChannel::class,
        'slack' => SlackChannel::class,
        'fake' => FakeChannel::class,
    ];

    protected function configure(): void
    {
        $this
            ->setName('notify')
            ->setDescription('It notifies based on conditions applied to the crawled result.')
            ->setHelp('Example: php bin/console --channels=email --maxNews=3 --maxRepetitions=10 --sleepingTime=300 YM GC EA')
            ->addArgument(
                'symbols',
                InputArgument::IS_ARRAY|InputArgument::REQUIRED,
                'Which stock symbols do you want to crawl?',
            )
            ->addOption(
                'channels',
                'c',
                InputArgument::OPTIONAL,
                'Channels to notify separated by comma [email,slack]',
                self::DEFAULT_CHANNEL,
            )
            ->addOption(
                'maxNews',
                'm',
                InputArgument::OPTIONAL,
                'Max number of news to fetch per crawled site',
                self::DEFAULT_MAX_NEWS_PER_CRAWLED_SITE,
            )
            ->addOption(
                'maxRepetitions',
                'r',
                InputArgument::OPTIONAL,
                'Max number repetitions for the loop',
                self::DEFAULT_MAX_REPETITIONS,
            )
            ->addOption(
                'sleepingTime',
                's',
                InputArgument::OPTIONAL,
                'Sleeping time in seconds',
                self::DEFAULT_SLEEPING_TIME_IN_SECONDS,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandInput = NotifyCommandInput::createFromInput($input);
        $channelNames = $this->mapChannelNames($commandInput->getChannelsAsString());

        $policy = $this->createPolicyForSymbols($commandInput->getSymbols());
        $facade = new StockTickerFacade();

        for ($i = 0; $i < $commandInput->getMaxRepetitions(); ++$i) {
            $this->printStartingIteration($output, $commandInput, $i);

            $notifyResult = $facade->sendNotifications(
                $channelNames,
                $policy,
                $commandInput->getMaxNews(),
            );

            $this->printNotifyResult($output, $notifyResult);
            $this->sleepWithPrompt($output, $commandInput->getSleepingTime());
        }

        $output->writeln('Already reached the max repetition limit of ' . $commandInput->getMaxRepetitions());

        return Command::SUCCESS;
    }

    private function mapChannelNames(string $channelsAsString): array
    {
        return array_filter(array_map(
            static fn (string $c): string => self::MAP_POSSIBLE_CHANNELS[$c] ?? '',
            explode(',', $channelsAsString),
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

    private function printStartingIteration(
        OutputInterface $output,
        NotifyCommandInput $commandInput,
        int $actualIteration,
    ): void {
        $output->writeln(sprintf('<comment>Looking for news in %s</comment>', implode(', ', $commandInput->getSymbols())));
        $output->writeln(sprintf('<comment>Completed %d of %d</comment>', $actualIteration, $commandInput->getMaxRepetitions()));
    }

    private function printNotifyResult(OutputInterface $output, NotifyResult $notifyResult): void
    {
        if ($notifyResult->isEmpty()) {
            $output->writeln('<question>~~~~ Nothing new here ~~~~</question>');

            return;
        }

        $output->writeln('===========================');
        $output->writeln('====== Notify result ======');
        $output->writeln('===========================');

        foreach ($notifyResult->conditionNamesGroupBySymbol() as $symbol => $conditionNames) {
            $quote = $notifyResult->quoteBySymbol($symbol);

            $companyName = ($quote->getCompanyName() !== null)
                ? $quote->getCompanyName()->getLongName() ?? ''
                : '';

            $symbol = $quote->getSymbol() ?? '';
            $output->writeln(sprintf('<options=bold,underscore>%s</> (%s)', $companyName, $symbol));

            foreach ($conditionNames as $conditionName) {
                $output->writeln("  - <info>{$conditionName}</info>");
            }
            $output->writeln('');
        }
        $output->writeln('');
    }

    private function sleepWithPrompt(OutputInterface $output, int $sec): void
    {
        $text = "<comment>Sleeping {$sec} seconds</comment>";
        $len = mb_strlen((string) $sec);

        for ($i = $sec; $i > 0; --$i) {
            $output->write(sprintf("{$text}: %0{$len}d\r", $i));
            sleep(1);
        }

        $output->writeln('');
        $output->writeln('<info>Awake again!</info>');
    }
}
