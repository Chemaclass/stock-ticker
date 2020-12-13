<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews;

use Chemaclass\TickerNews\Domain\Crawler\CompanyCrawler;
use Chemaclass\TickerNews\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\TickerNews\Domain\Notifier\Notifier;
use Chemaclass\TickerNews\Domain\Notifier\NotifierPolicy;

interface TickerNewsFactoryInterface
{
    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler;

    public function createNotifier(NotifierPolicy $policy): Notifier;
}
