<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\JsonExtractor\QuoteSummaryStore;

use Chemaclass\TickerNews\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\TickerNews\Domain\ReadModel\ExtractedFromJson;

final class RegularMarketChangePercent implements JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson
    {
        $quoteSummaryStore = $json['context']['dispatcher']['stores']['QuoteSummaryStore'];

        return ExtractedFromJson::fromString(
            (string) $quoteSummaryStore['price']['regularMarketChangePercent']['raw']
        );
    }
}
