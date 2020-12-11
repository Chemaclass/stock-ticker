<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;

final class PriceExtractor implements JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson
    {
        return ExtractedFromJson::fromString(
            (string) $json['context']['dispatcher']['stores']['QuoteSummaryStore']['financialData']['targetLowPrice']['fmt']
        );
    }
}
