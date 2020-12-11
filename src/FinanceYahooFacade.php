<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\Crawler\CrawlResult;
use Chemaclass\FinanceYahoo\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifierPolicy;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;

final class FinanceYahooFacade
{
    private FinanceYahooFactoryInterface $factory;

    public function __construct(FinanceYahooFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param list<SiteCrawlerInterface> $siteCrawlers
     * @param list<string> $tickerSymbols
     */
    public function crawlStock(array $siteCrawlers, array $tickerSymbols): CrawlResult
    {
        return $this->factory
            ->createCompanyCrawler(...$siteCrawlers)
            ->crawlStock(...$tickerSymbols);
    }

    public function notify(NotifierPolicy $policy, CrawlResult $companies): NotifyResult
    {
        return $this->factory
            ->createNotifier($policy)
            ->notify($companies);
    }
}
