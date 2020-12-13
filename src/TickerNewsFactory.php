<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews;

use Chemaclass\TickerNews\Domain\Crawler\CompanyCrawler;
use Chemaclass\TickerNews\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\TickerNews\Domain\Notifier\ChannelInterface;
use Chemaclass\TickerNews\Domain\Notifier\Notifier;
use Chemaclass\TickerNews\Domain\Notifier\NotifierPolicy;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TickerNewsFactory implements TickerNewsFactoryInterface
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
