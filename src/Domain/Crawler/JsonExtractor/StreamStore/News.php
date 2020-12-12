<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\StreamStore;

use Chemaclass\FinanceYahoo\Domain\Crawler\JsonExtractor\JsonExtractorInterface;
use Chemaclass\FinanceYahoo\Domain\ReadModel\ExtractedFromJson;
use DateTimeImmutable;
use DateTimeZone;

final class News implements JsonExtractorInterface
{
    private const DEFAULT_MAX_TEXT_LENGTH_CHARS = 180;

    private const DATETIME_FORMAT = 'Y-m-d H:i:s';

    private DateTimeZone $dateTimeZone;

    private ?int $maxNewsToFetch;

    private int $maxTextLengthChars;

    public function __construct(
        DateTimeZone $dateTimeZone,
        ?int $maxNewsToFetch = null,
        int $maxTextLengthChars = self::DEFAULT_MAX_TEXT_LENGTH_CHARS
    ) {
        $this->dateTimeZone = $dateTimeZone;
        $this->maxNewsToFetch = $maxNewsToFetch;
        $this->maxTextLengthChars = $maxTextLengthChars;
    }

    public function extractFromJson(array $json): ExtractedFromJson
    {
        $streams = $json['context']['dispatcher']['stores']['StreamStore']['streams'];
        $first = reset($streams);
        $streamItems = $first['data']['stream_items'];

        $articles = $this->filterOnlyArticles($streamItems);
        $sorted = $this->sortNewestFirst($this->extractInfo($articles));
        $limited = $this->limitByMaxToFetch($sorted);

        return ExtractedFromJson::fromArray($limited);
    }

    private function filterOnlyArticles(array $items): array
    {
        return array_filter(
            $items,
            static fn (array $i): bool => 'article' === $i['type']
        );
    }

    private function sortNewestFirst(array $extractInfo): array
    {
        usort(
            $extractInfo,
            static fn (array $a, array $b) => $b['fmtPubtime'] <=> $a['fmtPubtime']
        );

        return $extractInfo;
    }

    private function extractInfo(array $articles): array
    {
        $map = array_map(
            fn (array $i): array => [
                'fmtPubtime' => $this->normalizeDateTimeFromUnix($i['pubtime']),
                'timezone' => $this->dateTimeZone->getName(),
                'url' => $i['url'],
                'title' => $this->normalizeText($i['title']),
                'summary' => $this->normalizeText($i['summary']),
            ],
            $articles
        );

        return array_values($map);
    }

    private function normalizeDateTimeFromUnix(int $pubtime): string
    {
        $unixTime = (int) mb_substr((string) $pubtime, 0, -3);

        $dt = new DateTimeImmutable("@$unixTime");
        $dt->setTimeZone($this->dateTimeZone);

        return $dt->format(self::DATETIME_FORMAT);
    }

    private function normalizeText(string $text): string
    {
        if (mb_strlen($text) < $this->maxTextLengthChars) {
            return $text;
        }

        return mb_substr($text, 0, $this->maxTextLengthChars) . '...';
    }

    private function limitByMaxToFetch(array $info): array
    {
        if (null === $this->maxNewsToFetch) {
            return $info;
        }

        return array_slice($info, 0, $this->maxNewsToFetch);
    }
}
