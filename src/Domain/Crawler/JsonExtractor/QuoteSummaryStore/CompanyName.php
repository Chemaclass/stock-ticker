<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\QuoteSummaryStore;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;

final class CompanyName implements JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson
    {
        $quoteSummaryStore = $json['context']['dispatcher']['stores']['QuoteSummaryStore'];

        return ExtractedFromJson::fromString(
            (string) $quoteSummaryStore['price']['longName']
        );
    }
}
