<?php

declare(strict_types=1);

namespace App\Company;

use App\Company\Crawler\CrawlerInterface;
use App\Company\ReadModel\Company;
use App\Company\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CompanyCrawler
{
    private const REQUEST_METHOD = 'GET';

    private string $requestUrl;

    /** @var array<string, CrawlerInterface> */
    private array $crawlerInterfaces;

    /**
     * @param array<string, CrawlerInterface> $crawlerInterfaces
     */
    public function __construct(
        string $requestUrl,
        array $crawlerInterfaces
    ) {
        $this->requestUrl = $requestUrl;
        $this->crawlerInterfaces = $crawlerInterfaces;
    }

    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Company
    {
        $url = sprintf($this->requestUrl, $ticker->symbol());

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url)
            ->getContent();

        $summary = [];

        foreach ($this->crawlerInterfaces as $name => $crawler) {
            $summary[$name] = $crawler->crawlHtml($html);
        }

        return new Company($summary);
    }
}
