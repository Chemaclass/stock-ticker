<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Domain\Crawler\JsonExtractor\StreamStore;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\StreamStore\News;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use DateTimeZone;
use Generator;
use PHPUnit\Framework\TestCase;

final class NewsTest extends TestCase
{
    /**
     * @dataProvider providerExtractFromJson
     */
    public function testExtractFromJson(array $allItems, array $expected): void
    {
        $json = $this->createJsonWithItems($allItems);
        $news = new News(new DateTimeZone('Europe/Berlin'));

        self::assertEquals(
            ExtractedFromJson::fromArray($expected),
            $news->extractFromJson($json)
        );
    }

    public function providerExtractFromJson(): Generator
    {
        yield 'Only one add' => [
            'allItems' => [
                [
                    'type' => 'ad',
                    'title' => 'This is an add',
                    'pubtime' => 1607651748000,
                    'url' => 'url.com',
                    'summary' => 'A summary',
                ],
            ],
            'expected' => [],
        ];

        yield 'Only one non-add' => [
            'allItems' => [
                [
                    'type' => '-',
                    'title' => 'The title',
                    'pubtime' => 1607651748000,
                    'url' => 'url.com',
                    'summary' => 'A summary',
                ],
            ],
            'expected' => [
                [
                    'title' => 'The title',
                    'pubtime' => '2020-12-11 01:55:48',
                    'url' => 'url.com',
                    'summary' => 'A summary',
                ],
            ],
        ];

        yield 'A mix with adds and no adds' => [
            'allItems' => [
                [
                    'type' => '-',
                    'title' => 'The first title',
                    'pubtime' => 1607651748000,
                    'url' => 'url.1.com',
                    'summary' => 'A summary',
                ],
                [
                    'type' => 'ad',
                    'title' => 'This is an Add!',
                    'pubtime' => 1607651748000,
                    'url' => 'url.~1~.com',
                    'summary' => 'First summary',
                ],
                [
                    'type' => '-',
                    'title' => 'The second title',
                    'pubtime' => 1607651748000,
                    'url' => 'url.2.com',
                    'summary' => 'A second summary',
                ],
                [
                    'type' => 'ad',
                    'title' => 'This is another Add!',
                    'pubtime' => 1607651748000,
                    'url' => 'url.~2~.com',
                    'summary' => 'A summary',
                ],
            ],
            'expected' => [
                [
                    'title' => 'The first title',
                    'pubtime' => '2020-12-11 01:55:48',
                    'url' => 'url.1.com',
                    'summary' => 'A summary',
                ],
                [
                    'title' => 'The second title',
                    'pubtime' => '2020-12-11 01:55:48',
                    'url' => 'url.2.com',
                    'summary' => 'A second summary',
                ],
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
