<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;

interface JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson;
}
