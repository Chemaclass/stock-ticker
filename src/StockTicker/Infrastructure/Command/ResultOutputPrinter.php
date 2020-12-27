<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Infrastructure\Command;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Symfony\Component\Console\Output\OutputInterface;

final class ResultOutputPrinter
{
    public static function printCrawResult(OutputInterface $output, CrawlResult $crawlResult): void
    {
        if ($crawlResult->isEmpty()) {
            $output->writeln('Nothing new here...');

            return;
        }

        $output->writeln('~~~~~~~~~~~~~~~~~~~~~~~~~~');
        $output->writeln('~~~~~~ Crawl result ~~~~~~');
        $output->writeln('~~~~~~~~~~~~~~~~~~~~~~~~~~');

        foreach ($crawlResult->getCompaniesGroupedBySymbol() as $symbol => $quote) {
            $output->writeln($symbol);

            foreach ($quote->toArray() as $key => $value) {
                $output->writeln(sprintf('# %s => %s', $key, json_encode($value)));
            }

            $output->writeln('');
        }
    }

    public static function printNotifyResult(OutputInterface $output, NotifyResult $notifyResult): void
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
    }
}
