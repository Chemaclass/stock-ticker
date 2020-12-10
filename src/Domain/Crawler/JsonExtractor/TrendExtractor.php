<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;

final class TrendExtractor implements JsonExtractorInterface
{
    public static function name(): string
    {
        return 'recommendationTrend';
    }

    public function extractFromJson(array $json): ExtractedFromJson
    {
        return ExtractedFromJson::fromArray(
            $json['context']['dispatcher']['stores']['QuoteSummaryStore']['recommendationTrend']['trend']['0']
        );
    }
}
