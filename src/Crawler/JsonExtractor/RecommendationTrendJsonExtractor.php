<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;

final class RecommendationTrendJsonExtractor implements JsonExtractorInterface
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
