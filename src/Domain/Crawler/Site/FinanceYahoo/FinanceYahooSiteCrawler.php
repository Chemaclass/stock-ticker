<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo;

use function assert;
use Chemaclass\TickerNews\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\TickerNews\Domain\ReadModel\Site;
use Chemaclass\TickerNews\Domain\ReadModel\Symbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see "data/RootAppMainJsonExample.json" to see the structure of the `root.App.main` json.
 * @see https://jsoneditoronline.org/ to visualize and find what you are interested in.
 */
final class FinanceYahooSiteCrawler implements SiteCrawlerInterface
{
    private const REQUEST_METHOD = 'GET';

    private const REQUEST_URL = 'https://finance.yahoo.com/quote/%s';

    /** @var array<int|string,JsonExtractorInterface> */
    private array $jsonExtractors;

    public function __construct(array $jsonExtractors)
    {
        foreach ($jsonExtractors as $extractor) {
            assert($extractor instanceof JsonExtractorInterface);
        }

        $this->jsonExtractors = $jsonExtractors;
    }

    public function crawl(HttpClientInterface $httpClient, Symbol $symbol): Site
    {
        $url = sprintf(self::REQUEST_URL, $symbol->toString());

        $html = $httpClient
            ->request(self::REQUEST_METHOD, $url)
            ->getContent();

        preg_match('/root\.App\.main\ =\ (?<json>.*);/m', $html, $matches);

        $json = (array) json_decode($matches['json'], true);
        $data = [];

        foreach ($this->jsonExtractors as $name => $extractor) {
            $name = is_int($name) ? get_class($extractor) : $name;
            $data[$name] = $extractor->extractFromJson($json);
        }

        return new Site($data);
    }
}
