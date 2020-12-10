<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Crawler\JsonExtractor\CompanyNameJsonExtractor;
use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;
use PHPUnit\Framework\TestCase;

final class CompanyNameJsonExtractorTest extends TestCase
{
    public function testName(): void
    {
        self::assertSame('name', CompanyNameJsonExtractor::name());
    }

    public function testExtractFromJson(): void
    {
        $json = $this->createJsonCompanyName('Example company name, Inc');

        self::assertEquals(
            ExtractedFromJson::fromString('Example company name, Inc'),
            (new CompanyNameJsonExtractor())->extractFromJson($json)
        );
    }

    private function createJsonCompanyName(string $shortName): array
    {
        return [
            'context' => [
                'dispatcher' => [
                    'stores' => [
                        'QuoteSummaryStore' => [
                            'price' => [
                                'shortName' => $shortName,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
