<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\WriteModel;

use Chemaclass\StockTicker\Domain\WriteModel\News;
use PHPUnit\Framework\TestCase;

final class NewsTest extends TestCase
{
    public function testToArray(): void
    {
        $array = [
            'datetime' => 'example datetime',
            'timezone' => 'example timezone',
            'url' => 'example url',
            'title' => 'example title',
            'summary' => 'example summary',
            'publisher' => 'example publisher',
            'author' => 'example author',
            'source' => 'example source',
            'images' => [
                [
                    'url' => 'example.img.url',
                ],
            ],
        ];

        $model = (new News())->fromArray($array);

        self::assertEquals($array, $model->toArray());
        self::assertEquals($array['datetime'], $model->getDatetime());
        self::assertEquals($array['timezone'], $model->getTimezone());
        self::assertEquals($array['url'], $model->getUrl());
        self::assertEquals($array['title'], $model->getTitle());
        self::assertEquals($array['summary'], $model->getSummary());
        self::assertEquals($array['publisher'], $model->getPublisher());
        self::assertEquals($array['author'], $model->getAuthor());
        self::assertEquals($array['source'], $model->getSource());
        self::assertEquals($array['images'], $model->getImages());
    }
}
