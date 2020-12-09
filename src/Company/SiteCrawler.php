<?php

declare(strict_types=1);

namespace App\Company;

use App\Company\Crawler\CrawlerInterface;
use App\Company\ReadModel\Site;
use App\Company\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SiteCrawler
{
    private const REQUEST_METHOD = 'GET';

    private string $requestUrl;

    /** @var array<string, CrawlerInterface> */
    private array $crawlers;

    /**
     * @param array<string, CrawlerInterface> $crawlers
     */
    public function __construct(
        string $requestUrl,
        array $crawlers
    ) {
        $this->requestUrl = $requestUrl;
        $this->crawlers = $crawlers;
    }

    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
    {
        $url = sprintf($this->requestUrl, $ticker->symbol());

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url)
            ->getContent();

        $crawled = [];

        foreach ($this->crawlers as $name => $crawler) {
            $crawled[$name] = $crawler->crawlHtml($html);
        }

        return new Site($crawled);
    }
}
