<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\NotifierPolicy;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

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
     *
     * @return array<string,Company>
     */
    public function crawlStock(array $siteCrawlers, array $tickerSymbols): array
    {
        return $this->factory
            ->createCompanyCrawler(...$siteCrawlers)
            ->crawlStock(...$tickerSymbols);
    }

    /**
     * @param $companies array<string,Company>
     */
    public function notify(NotifierPolicy $policy, array $companies): NotifyResult
    {
        return $this->factory
            ->createNotifier($policy)
            ->notify($companies);
    }
}
