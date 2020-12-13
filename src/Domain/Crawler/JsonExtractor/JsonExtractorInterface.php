<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\JsonExtractor;

use Chemaclass\TickerNews\Domain\ReadModel\ExtractedFromJson;

interface JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson;
}
