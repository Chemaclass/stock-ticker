<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\RouteStore;

use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;

final class ExternalUrl implements JsonExtractorInterface
{
    public function extractFromJson(array $json): string
    {
        $routeStore = $json['context']['dispatcher']['stores']['RouteStore'];

        return  (string) $routeStore['currentNavigate']['externalUrl'];
    }
}
