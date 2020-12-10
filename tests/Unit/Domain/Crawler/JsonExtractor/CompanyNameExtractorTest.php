<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Domain\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\CompanyNameExtractor;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use PHPUnit\Framework\TestCase;

final class CompanyNameExtractorTest extends TestCase
{
    public function testName(): void
    {
        self::assertSame('name', CompanyNameExtractor::name());
    }

    public function testExtractFromJson(): void
    {
        $json = $this->createJsonCompanyName('Example company name, Inc');

        self::assertEquals(
            ExtractedFromJson::fromString('Example company name, Inc'),
            (new CompanyNameExtractor())->extractFromJson($json)
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
