<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo;

use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\ReadModel\Site;
use Chemaclass\StockTicker\Domain\ReadModel\Symbol;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function assert;
use function get_class;
use function is_int;

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
        $data = [
            'symbol' => $symbol->toString(),
        ];

        foreach ($this->jsonExtractors as $name => $extractor) {
            $name = is_int($name) ? get_class($extractor) : $name;
            $data[$name] = $extractor->extractFromJson($json);
        }

        return new Site($data);
    }
}
