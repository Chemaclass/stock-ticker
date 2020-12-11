<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Domain\Crawler\CompanyCrawler;
use Chemaclass\FinanceYahoo\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\Notifier;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifierPolicy;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class FinanceYahooFactory implements FinanceYahooFactoryInterface
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
