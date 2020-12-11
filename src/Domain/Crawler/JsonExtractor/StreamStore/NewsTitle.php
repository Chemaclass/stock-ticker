<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\StreamStore;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;

final class NewsTitle implements JsonExtractorInterface
{
    public function extractFromJson(array $json): ExtractedFromJson
    {
        $streams = $json['context']['dispatcher']['stores']['StreamStore']['streams'];
        $first = reset($streams);
        $items = $first['data']['stream_items'];

        $titles = $this->mapTitles($this->filterAdds($items));

        return ExtractedFromJson::fromArray($titles);
    }

    private function filterAdds(array $items): array
    {
        return array_filter($items, static fn (array $i): bool => 'ad' !== $i['type']);
    }

    private function mapTitles(array $items): array
    {
        return array_values(
            array_map(static fn (array $i): string => $i['title'], $items)
        );
    }
}
