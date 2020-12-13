<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo;

interface JsonExtractorInterface
{
    public function extractFromJson(array $json): array;
}
