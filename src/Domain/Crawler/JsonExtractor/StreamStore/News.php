<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\StreamStore;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use DateTimeImmutable;
use DateTimeZone;

final class News implements JsonExtractorInterface
{
    private const MAX_TEXT_LENGTH_CHARS = 200;

    private DateTimeZone $dateTimeZone;

    private ?int $maxNewsToFetch;

    public function __construct(DateTimeZone $dateTimeZone, ?int $maxNewsToFetch = null)
    {
        $this->dateTimeZone = $dateTimeZone;
        $this->maxNewsToFetch = $maxNewsToFetch;
    }

    public function extractFromJson(array $json): ExtractedFromJson
    {
        $streams = $json['context']['dispatcher']['stores']['StreamStore']['streams'];
        $first = reset($streams);
        $streamItems = $first['data']['stream_items'];
        $items = $this->filterAdds($streamItems);
        $info = $this->sortByPubtime($this->extractInfo($items));
        $limited = $this->limitByMaxToFetch($info);

        return ExtractedFromJson::fromArray($limited);
    }

    private function filterAdds(array $items): array
    {
        return array_filter($items, static fn (array $i): bool => 'ad' !== $i['type']);
    }

    private function extractInfo(array $items): array
    {
        $map = array_map(
            function (array $i): array {
                return [
                    'pubtime' => $this->normalizeDateTimeFromUnix($i['pubtime']),
                    'url' => $i['url'],
                    'title' => $this->normalizeText($i['title']),
                    'summary' => $this->normalizeText($i['summary']),
                ];
            },
            $items
        );

        return array_values($map);
    }

    private function normalizeText(string $text): string
    {
        if (mb_strlen($text) < self::MAX_TEXT_LENGTH_CHARS) {
            return $text;
        }

        return mb_substr($text, 0, self::MAX_TEXT_LENGTH_CHARS) . '...';
    }

    private function normalizeDateTimeFromUnix(int $pubtime): string
    {
        $unixTime = (int) mb_substr((string) $pubtime, 0, -3);

        $dt = new DateTimeImmutable("@$unixTime");
        $dt->setTimeZone($this->dateTimeZone);

        return $dt->format('Y-m-d H:i:s');
    }

    private function sortByPubtime(array $extractInfo): array
    {
        usort(
            $extractInfo,
            fn (array $a, array $b) => $b['pubtime'] <=> $a['pubtime']
        );

        return $extractInfo;
    }

    private function limitByMaxToFetch(array $info): array
    {
        if (null === $this->maxNewsToFetch) {
            return $info;
        }

        return array_slice($info, 0, $this->maxNewsToFetch);
    }
}
