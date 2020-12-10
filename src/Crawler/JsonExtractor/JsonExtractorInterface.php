<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;

interface JsonExtractorInterface
{
    public static function name(): string;

    public function extractFromJson(array $json): ExtractedFromJson;
}
