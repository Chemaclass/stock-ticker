<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Crawler\CompanyCrawler;
use Chemaclass\FinanceYahoo\Crawler\SiteCrawlerInterface;

interface FinanceYahooFactoryInterface
{
    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler;
}
