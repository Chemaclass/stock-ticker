<?php

declare(strict_types=1);

namespace Chemaclass\TickerNewsTests\Unit\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\StreamStore;

use Chemaclass\TickerNews\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\StreamStore\News;
use Chemaclass\TickerNews\Domain\ReadModel\ExtractedFromJson;
use DateTimeZone;
use Generator;
use PHPUnit\Framework\TestCase;

final class NewsTest extends TestCase
{
    private const EXAMPLE_UNIX_PUBTIME = 1607651748000;

    private const EXAMPLE_FORMATTED_DATETIME = '2020-12-11 01:55:48';

    private const EXAMPLE_TIMEZONE = 'Europe/Berlin';

    /**
     * @dataProvider providerExtractFromJson
     */
    public function testExtractFromJson(array $allItems, array $expected): void
    {
        $json = $this->createJsonWithItems($allItems);
        $news = new News(new DateTimeZone(self::EXAMPLE_TIMEZONE));

        self::assertEquals(
            ExtractedFromJson::fromArray($expected),
            $news->extractFromJson($json)
        );
    }

    public function providerExtractFromJson(): Generator
    {
        yield 'One add' => [
            'allItems' => [
                [
                    'type' => 'ad',
                    'title' => 'This is an add',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.com',
                    'summary' => 'A summary',
                ],
            ],
            'expected' => [],
        ];

        yield 'One video' => [
            'allItems' => [
                [
                    'type' => 'video',
                    'title' => 'This is a video',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.com',
                    'summary' => 'A summary',
                ],
            ],
            'expected' => [],
        ];

        yield 'One article' => [
            'allItems' => [
                [
                    'type' => 'article',
                    'title' => 'The title',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.com',
                    'summary' => 'A summary',
                ],
            ],
            'expected' => [
                [
                    'title' => 'The title',
                    'fmtPubtime' => self::EXAMPLE_FORMATTED_DATETIME,
                    'url' => 'url.com',
                    'summary' => 'A summary',
                    'timezone' => self::EXAMPLE_TIMEZONE,
                ],
            ],
        ];

        yield 'A mix with add, video and articles' => [
            'allItems' => [
                [
                    'type' => '-',
                    'title' => 'Unknown type title',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.~0~.com',
                    'summary' => 'summary',
                ],
                [
                    'type' => 'ad',
                    'title' => 'This is an Add!',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.~1~.com',
                    'summary' => 'summary',
                ],
                [
                    'type' => 'article',
                    'title' => 'The first title',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.1.com',
                    'summary' => 'First summary',
                ],
                [
                    'type' => 'video',
                    'title' => 'This is another Add!',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.~2~.com',
                    'summary' => 'summary',
                ],
                [
                    'type' => 'article',
                    'title' => 'The second title',
                    'pubtime' => self::EXAMPLE_UNIX_PUBTIME,
                    'url' => 'url.2.com',
                    'summary' => 'Second summary',
                ],
            ],
            'expected' => [
                [
                    'title' => 'The first title',
                    'fmtPubtime' => self::EXAMPLE_FORMATTED_DATETIME,
                    'url' => 'url.1.com',
                    'summary' => 'First summary',
                    'timezone' => self::EXAMPLE_TIMEZONE,
                ],
                [
                    'title' => 'The second title',
                    'fmtPubtime' => self::EXAMPLE_FORMATTED_DATETIME,
                    'url' => 'url.2.com',
                    'summary' => 'Second summary',
                    'timezone' => self::EXAMPLE_TIMEZONE,
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
