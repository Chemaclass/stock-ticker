<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company;

use Chemaclass\FinanceYahoo\Company\HtmlCrawler\CrawlerInterface;
use Chemaclass\FinanceYahoo\Company\ReadModel\Site;
use Chemaclass\FinanceYahoo\Company\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HtmlSiteCrawler implements SiteCrawlerInterface
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
