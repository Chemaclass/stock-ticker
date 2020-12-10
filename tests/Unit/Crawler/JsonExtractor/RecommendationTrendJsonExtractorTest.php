<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Crawler\JsonExtractor\RecommendationTrendJsonExtractor;
use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;
use PHPUnit\Framework\TestCase;

final class RecommendationTrendJsonExtractorTest extends TestCase
{
    public function testName(): void
    {
        self::assertSame('recommendationTrend', RecommendationTrendJsonExtractor::name());
    }

    public function testExtractFromJson(): void
    {
        $recommendationTrend = ['k' => 'v'];
        $json = $this->createJsonWithPrice($recommendationTrend);

        self::assertEquals(
            ExtractedFromJson::fromArray($recommendationTrend),
            (new RecommendationTrendJsonExtractor())->extractFromJson($json)
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
