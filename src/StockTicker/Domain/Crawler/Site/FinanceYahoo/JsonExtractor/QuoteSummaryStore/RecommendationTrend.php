<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\QuoteSummaryStore;

use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;

final class RecommendationTrend implements JsonExtractorInterface
{
    public function extractFromJson(array $json): array
    {
        $quoteSummaryStore = $json['context']['dispatcher']['stores']['QuoteSummaryStore'] ?? [];

        return $quoteSummaryStore['recommendationTrend']['trend'] ?? [];
    }
}
