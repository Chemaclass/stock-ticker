<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\NotifierPolicy;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

final class FinanceYahooFacade
{
    private FinanceYahooConfigInterface $config;

    private FinanceYahooFactoryInterface $factory;

    public function __construct(
        FinanceYahooConfigInterface $config,
        FinanceYahooFactoryInterface $factory
    ) {
        $this->config = $config;
        $this->factory = $factory;
    }

    /**
     * @return array<string,Company>
     */
    public function crawlStock(SiteCrawlerInterface ...$siteCrawlers): array
    {
        return $this->factory
            ->createCompanyCrawler(...$siteCrawlers)
            ->crawlStock(...$this->config->getTickers());
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
