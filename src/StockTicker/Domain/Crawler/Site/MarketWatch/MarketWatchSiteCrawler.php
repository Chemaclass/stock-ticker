<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\MarketWatch;

use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MarketWatchSiteCrawler implements SiteCrawlerInterface
{
    private const REQUEST_METHOD = 'GET';

    private const REQUEST_URL = 'https://www.marketwatch.com/investing/stock/%s';

    /** @var HtmlCrawlerInterface[] */
    private array $crawlers;

    public function __construct(array $crawlers)
    {
        $this->crawlers = $crawlers;
    }

    public function crawl(HttpClientInterface $httpClient, Symbol $symbol): Site
    {
        $symbol = mb_strtolower($symbol->toString());
        $url = sprintf(self::REQUEST_URL, $symbol);

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url)
            ->getContent($throw = false);

        $crawled = [];

        foreach ($this->crawlers as $name => $crawler) {
            $crawled[$name] = $crawler->crawlHtml($html);
        }

        return new Site($crawled);
    }
}
