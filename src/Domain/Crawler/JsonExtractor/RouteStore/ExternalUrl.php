<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\RouteStore;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;

final class ExternalUrl implements JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson
    {
        $routeStore = $json['context']['dispatcher']['stores']['RouteStore'];

        return ExtractedFromJson::fromString(
            (string) $routeStore['currentNavigate']['externalUrl']
        );
    }
}
