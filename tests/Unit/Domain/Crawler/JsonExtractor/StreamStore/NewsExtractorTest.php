<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Domain\Crawler\JsonExtractor\StreamStore;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\StreamStore\NewsTitle;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use Generator;
use PHPUnit\Framework\TestCase;

final class NewsExtractorTest extends TestCase
{
    /**
     * @dataProvider providerExtractFromJson
     */
    public function testExtractFromJson(array $allItems, array $expected): void
    {
        $json = $this->createJsonWithItems($allItems);

        self::assertEquals(
            ExtractedFromJson::fromArray($expected),
            (new NewsTitle())->extractFromJson($json)
        );
    }

    public function providerExtractFromJson(): Generator
    {
        yield 'Only one add' => [
            'allItems' => [
                [
                    'type' => 'ad',
                    'title' => 'This is an add',
                ],
            ],
            'expected' => [],
        ];

        yield 'Only one non-add' => [
            'allItems' => [
                [
                    'type' => '-',
                    'title' => 'The title',
                ],
            ],
            'expected' => [
                'The title',
            ],
        ];

        yield 'A mix with adds and no adds' => [
            'allItems' => [
                [
                    'type' => '-',
                    'title' => 'The first title',
                ],
                [
                    'type' => 'ad',
                    'title' => 'This is an Add!',
                ],
                [
                    'type' => '-',
                    'title' => 'The second title',
                ],
                [
                    'type' => 'ad',
                    'title' => 'This is another Add!',
                ],
            ],
            'expected' => [
                'The first title',
                'The second title',
            ],
        ];
    }

    private function createJsonWithItems(array $allItems): array
    {
        $streamStore = [
            'streams' => [
                [
                    'data' => [
                        'stream_items' => $allItems,
                    ],
                ],
            ],
        ];

        return [
            'context' => [
                'dispatcher' => [
                    'stores' => [
                        'StreamStore' => $streamStore,
                    ],
                ],
            ],
        ];
    }
}
