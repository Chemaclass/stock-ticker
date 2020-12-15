<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo;

interface JsonExtractorInterface
{
    public function extractFromJson(array $json): array;
}
