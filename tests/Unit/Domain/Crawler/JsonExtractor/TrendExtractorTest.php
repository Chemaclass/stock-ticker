<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Domain\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\TrendExtractor;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use PHPUnit\Framework\TestCase;

final class TrendExtractorTest extends TestCase
{
    public function testExtractFromJson(): void
    {
        $recommendationTrend = ['k' => 'v'];
        $json = $this->createJsonWithPrice($recommendationTrend);

        self::assertEquals(
            ExtractedFromJson::fromArray($recommendationTrend),
            (new TrendExtractor())->extractFromJson($json)
        );
    }

    private function createJsonWithPrice(array $recommendationTrend): array
    {
        return [
            'context' => [
                'dispatcher' => [
                    'stores' => [
                        'QuoteSummaryStore' => [
                            'recommendationTrend' => [
                                'trend' => [
                                    '0' => $recommendationTrend,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
