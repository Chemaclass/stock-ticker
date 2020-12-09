<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company;

use Chemaclass\FinanceYahoo\Crawler\SiteCrawlerInterface;

interface CompanyCrawlerFactoryInterface
{
    public function createWithCrawlers(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler;
}
