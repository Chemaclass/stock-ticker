<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Company;

use Chemaclass\FinanceYahoo\Company\ReadModel\Site;
use Chemaclass\FinanceYahoo\Company\ReadModel\Ticker;
use Closure;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RootAppJsonCrawler implements SiteCrawlerInterface
{
    private const REQUEST_METHOD = 'GET';

    private string $requestUrl;

    private Closure $jsonExtractor;

    public function __construct(string $requestUrl, Closure $jsonExtractor)
    {
        $this->requestUrl = $requestUrl;
        $this->jsonExtractor = $jsonExtractor;
    }

    public function crawl(HttpClientInterface $httpClient, Ticker $ticker): Site
    {
        $url = sprintf($this->requestUrl, $ticker->symbol());

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url)
            ->getContent();

        preg_match('/root\.App\.main\ =\ (?<json>.*);/m', $html, $matches);

        $json = json_decode($matches['json'], true);
        $data = $this->jsonExtractor->call($this, $json);

        return new Site($data);
    }
}
