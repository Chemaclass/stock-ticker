<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Infrastructure\Command;

use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\Notifier\Policy\Condition\RecentNewsWasFound;
use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;
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
    private const DEFAULT_SLEEPING_TIME_IN_SECONDS = 60;

    protected function configure(): void
    {
        $this
            ->setName('notify')
            ->setDescription('It notifies based on conditions applied to the crawled result')
            ->addArgument(
                'symbols',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Which stock symbols do you want to crawl?'
            )
            ->addOption(
                'maxNews',
                'm',
                InputArgument::OPTIONAL,
                'Max number of news to fetch',
                self::DEFAULT_MAX_NEWS_TO_FETCH
            )
            ->addOption(
                'sleepingTime',
                's',
                InputArgument::OPTIONAL,
                'Sleeping time in seconds',
                self::DEFAULT_SLEEPING_TIME_IN_SECONDS
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var string[] $symbols */
        $symbols = (array) $input->getArgument('symbols');
        $maxNews = (int) $input->getOption('maxNews');
        $sleepingTime = (int) $input->getOption('sleepingTime');

        $policy = $this->createPolicyForSymbols($symbols);

        $channels = [
            EmailChannel::class,
            //SlackChannel::class,
        ];

        $facade = $this->createStockTickerFacade();

        while (true) {
            $output->writeln(sprintf('Looking for news in %s ...', implode(', ', $symbols)));

            $result = $facade->sendNotifications($channels, $policy, $maxNews);

            $this->printNotifyResult($output, $result);
            $this->sleepWithPrompt($output, $sleepingTime);
        }
    }

    /**
     * @param string[] $symbols
     */
    private function createPolicyForSymbols(array $symbols): NotifierPolicy
    {
        $conditions = array_fill_keys($symbols, new PolicyGroup([
            'More news was found' => new RecentNewsWasFound(),
        ]));

        return new NotifierPolicy($conditions);
    }

    private function createStockTickerFacade(): StockTickerFacade
    {
        return new StockTickerFacade(
            new StockTickerFactory(
                new StockTickerConfig(
                    dirname(__DIR__) . '/Templates',
                    $_ENV
                )
            ),
        );
    }

    private function printNotifyResult(OutputInterface $output, NotifyResult $notifyResult): void
    {
        if ($notifyResult->isEmpty()) {
            $output->writeln(' ~~~ Nothing new here...');

            return;
        }

        $output->writeln('===========================');
        $output->writeln('====== Notify result ======');
        $output->writeln('===========================');

        foreach ($notifyResult->conditionNamesGroupBySymbol() as $symbol => $conditionNames) {
            $output->writeln($symbol);
            $output->writeln('Conditions:');

            foreach ($conditionNames as $conditionName) {
                $output->writeln(sprintf('  - %s', $conditionName));
            }

            $output->writeln('');
        }

        $output->writeln('');
    }

    private function sleepWithPrompt(OutputInterface $output, int $sec): void
    {
        $output->writeln("Sleeping {$sec} seconds...");
        $len = mb_strlen((string) $sec);

        for ($i = $sec; $i > 0; $i--) {
            $output->write(sprintf("%0{$len}d\r", $i));
            sleep(1);
        }

        $output->writeln('Awake again!');
    }
}
