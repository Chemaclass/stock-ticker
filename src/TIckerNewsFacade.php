<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews;

use Chemaclass\TickerNews\Domain\Crawler\CrawlResult;
use Chemaclass\TickerNews\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\TickerNews\Domain\Notifier\NotifierPolicy;
use Chemaclass\TickerNews\Domain\Notifier\NotifyResult;

final class TickerNewsFacade
{
    private TickerNewsFactoryInterface $factory;

    public function __construct(TickerNewsFactoryInterface $factory)
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
