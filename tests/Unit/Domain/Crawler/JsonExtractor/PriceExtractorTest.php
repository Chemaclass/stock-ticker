<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Domain\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\PriceExtractor;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use PHPUnit\Framework\TestCase;

final class PriceExtractorTest extends TestCase
{
    public function testExtractFromJson(): void
    {
        $json = $this->createJsonWithPrice('11.6501');

        self::assertEquals(
            ExtractedFromJson::fromString('11.6501'),
            (new PriceExtractor())->extractFromJson($json)
        );
    }

    private function createJsonWithPrice(string $fmtPrice): array
    {
        return [
            'context' => [
                'dispatcher' => [
                    'stores' => [
                        'QuoteSummaryStore' => [
                            'financialData' => [
                                'targetLowPrice' => [
                                    'fmt' => $fmtPrice,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
