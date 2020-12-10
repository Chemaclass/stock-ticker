<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\Crawler\CompanyCrawler;
use Chemaclass\FinanceYahoo\Domain\Crawler\SiteCrawlerInterface;

interface FinanceYahooFactoryInterface
{
    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler;
}
