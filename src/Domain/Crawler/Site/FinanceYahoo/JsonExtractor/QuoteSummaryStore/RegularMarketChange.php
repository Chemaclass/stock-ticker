<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\QuoteSummaryStore;

use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;

final class RegularMarketChange implements JsonExtractorInterface
{
    public function extractFromJson(array $json): string
    {
        $quoteSummaryStore = $json['context']['dispatcher']['stores']['QuoteSummaryStore'];

        return(string) $quoteSummaryStore['price']['regularMarketChange']['raw'];
    }
}
