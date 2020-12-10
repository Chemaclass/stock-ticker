<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Company\CompanyCrawlerFactoryInterface;
use Chemaclass\FinanceYahoo\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\ReadModel\Company;

final class FinanceYahooFacade
{
    private FinanceYahooConfigInterface $config;

    private CompanyCrawlerFactoryInterface $companyCrawlerFactory;

    public function __construct(
        FinanceYahooConfigInterface $config,
        CompanyCrawlerFactoryInterface $companyCrawlerFactory
    ) {
        $this->config = $config;
        $this->companyCrawlerFactory = $companyCrawlerFactory;
    }

    /**
     * @return Company[]
     */
    public function crawlStock(SiteCrawlerInterface ...$siteCrawlers): array
    {
        return $this->companyCrawlerFactory
            ->createWithCrawlers(...$siteCrawlers)
            ->crawlStock(...$this->config->getTickers());
    }
}
