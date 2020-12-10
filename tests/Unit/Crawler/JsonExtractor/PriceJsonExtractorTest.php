<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Crawler\JsonExtractor\PriceJsonExtractor;
use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;
use PHPUnit\Framework\TestCase;

final class PriceJsonExtractorTest extends TestCase
{
    public function testName(): void
    {
        self::assertSame('price', PriceJsonExtractor::name());
    }

    public function testExtractFromJson(): void
    {
        $json = $this->createJsonWithPrice('11.6501');

        self::assertEquals(
            ExtractedFromJson::fromString('11.6501'),
            (new PriceJsonExtractor())->extractFromJson($json)
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
