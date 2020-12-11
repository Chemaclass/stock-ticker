<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;

final class CompanyNameExtractor implements JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson
    {
        return ExtractedFromJson::fromString(
            $json['context']['dispatcher']['stores']['QuoteSummaryStore']['price']['shortName']
        );
    }
}
