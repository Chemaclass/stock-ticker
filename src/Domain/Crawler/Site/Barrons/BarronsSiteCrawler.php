<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\Barrons;

use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BarronsSiteCrawler implements SiteCrawlerInterface
{
    private const REQUEST_METHOD = 'GET';

    private const REQUEST_URL = 'https://www.barrons.com/quote/stock/%s';

    private const EXAMPLE_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36';

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
            ->request(self::REQUEST_METHOD, $url, $this->buildRequestHeaders())
            ->getContent();

        $crawled = [];

        foreach ($this->crawlers as $name => $crawler) {
            $crawled[$name] = $crawler->crawlHtml($html);
        }

        return new Site($crawled);
    }

    private function buildRequestHeaders(): array
    {
        return [
            'headers' => [
                'User-Agent' => self::EXAMPLE_USER_AGENT,
            ],
        ];
    }
}
