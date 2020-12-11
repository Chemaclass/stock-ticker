<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\Crawler\CompanyCrawler;
use Chemaclass\FinanceYahoo\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\Notifier;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifierPolicy;

interface FinanceYahooFactoryInterface
{
    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler;

    public function createNotifier(NotifierPolicy $policy): Notifier;
}
