<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\CompanyCrawler;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\Notifier;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class StockTickerFactory implements StockTickerFactoryInterface
{
    private HttpClientInterface $httpClient;

    /** @var ChannelInterface[] */
    private array $channels;

    public function __construct(
        HttpClientInterface $httpClient,
        ChannelInterface ...$channels
    ) {
        $this->httpClient = $httpClient;
        $this->channels = $channels;
    }

    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): CompanyCrawler
    {
        return new CompanyCrawler($this->httpClient, ...$siteCrawlers);
    }

    public function createNotifier(NotifierPolicy $policy): Notifier
    {
        return new Notifier($policy, ...$this->channels);
    }
}
