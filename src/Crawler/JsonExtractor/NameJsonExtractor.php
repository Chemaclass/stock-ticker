<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;

final class NameJsonExtractor implements JsonExtractorInterface
{
    public static function name(): string
    {
        return 'name';
    }

    public function extractFromJson(array $json): ExtractedFromJson
    {
        return ExtractedFromJson::fromString(
            $json['context']['dispatcher']['stores']['QuoteSummaryStore']['price']['shortName']
        );
    }
}
