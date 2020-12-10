<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler;

use Chemaclass\FinanceYahoo\ReadModel\Site;
use Chemaclass\FinanceYahoo\ReadModel\Ticker;
use Closure;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see "data/RootAppMainJsonExample.json" to see the structure of the `root.App.main` json.
 * @see https://jsoneditoronline.org/ to visualize and find what you are interested in.
 */
final class RootAppJsonCrawler implements SiteCrawlerInterface
{
    private const REQUEST_METHOD = 'GET';

    private const REQUEST_URL = 'https://finance.yahoo.com/quote/%s';

    private Closure $jsonExtractor;

    public function __construct(Closure $jsonExtractor)
    {
        $this->jsonExtractor = $jsonExtractor;
    }

    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
    {
        $url = sprintf(self::REQUEST_URL, $ticker->symbol());

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url)
            ->getContent();

        preg_match('/root\.App\.main\ =\ (?<json>.*);/m', $html, $matches);

        $json = (array) json_decode($matches['json'], true);
        $data = (array) $this->jsonExtractor->call($this, $json);

        return new Site($data);
    }
}
