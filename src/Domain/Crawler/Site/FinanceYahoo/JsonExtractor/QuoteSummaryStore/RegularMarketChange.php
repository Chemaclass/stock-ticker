<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\QuoteSummaryStore;

use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;
use Chemaclass\TickerNews\Domain\ReadModel\ExtractedFromJson;

final class RegularMarketChange implements JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson
    {
        $quoteSummaryStore = $json['context']['dispatcher']['stores']['QuoteSummaryStore'];

        return ExtractedFromJson::fromString(
            (string) $quoteSummaryStore['price']['regularMarketChange']['raw']
        );
    }
}
