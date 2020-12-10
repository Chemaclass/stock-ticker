<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\JsonExtractor;

use Chemaclass\FinanceYahoo\ReadModel\ExtractedFromJson;

final class NewsJsonExtractor implements JsonExtractorInterface
{
    public static function name(): string
    {
        return 'news';
    }

    public function extractFromJson(array $json): ExtractedFromJson
    {
        $streams = $json['context']['dispatcher']['stores']['StreamStore']['streams'];
        $first = reset($streams);
        $items = $first['data']['stream_items'];

        $titles = array_filter($items, static fn (array $i): bool => 'ad' !== $i['type']);
        $titles = array_map(static fn (array $i): string => $i['title'], $titles);

        return ExtractedFromJson::fromArray($titles);
    }
}
