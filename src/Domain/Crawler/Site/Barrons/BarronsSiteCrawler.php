<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\Barrons;

use Chemaclass\TickerNews\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\TickerNews\Domain\ReadModel\Site;
use Chemaclass\TickerNews\Domain\ReadModel\Ticker;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BarronsSiteCrawler implements SiteCrawlerInterface
{
    private const REQUEST_METHOD = 'GET';

    private const REQUEST_URL = 'https://www.barrons.com/quote/stock/%s';

    /** @var HtmlCrawlerInterface[] */
    private array $crawlers;

    public function __construct(array $crawlers)
    {
        $this->crawlers = $crawlers;
    }

    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
    {
        $symbol = mb_strtolower($ticker->symbol());

        $url = sprintf(self::REQUEST_URL, $symbol);

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
                ],
            ])
            ->getContent();

        $crawled = [];

        foreach ($this->crawlers as $name => $crawler) {
            $crawled[$name] = $crawler->crawlHtml($html);
        }

        return new Site($crawled);
    }
}
