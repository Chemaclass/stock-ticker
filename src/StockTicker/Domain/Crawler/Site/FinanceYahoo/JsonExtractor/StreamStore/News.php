<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\StreamStore;

use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizer;
use DateTimeImmutable;

final class News implements JsonExtractorInterface
{
    private const SOURCE = 'FinanceYahoo';

    private NewsNormalizer $newsNormalizer;

    public function __construct(NewsNormalizer $newsNormalizer)
    {
        $this->newsNormalizer = $newsNormalizer;
    }

    public function extractFromJson(array $json): array
    {
        $streams = $json['context']['dispatcher']['stores']['StreamStore']['streams'];
        $first = reset($streams);
        $streamItems = $first['data']['stream_items'];

        $articles = $this->filterOnlyArticles($streamItems);
        $sorted = $this->sortNewestFirst($this->extractInfo($articles));

        return $this->newsNormalizer->limitByMaxToFetch($sorted);
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
            static fn (array $a, array $b) => $b['datetime'] <=> $a['datetime']
        );

        return $extractInfo;
    }

    private function extractInfo(array $articles): array
    {
        $map = array_map(
            fn (array $i): array => [
                'datetime' => $this->normalizeDateTimeFromUnix($i['pubtime']),
                'timezone' => $this->newsNormalizer->getTimeZoneName(),
                'url' => $i['url'],
                'title' => $this->newsNormalizer->normalizeText($i['title']),
                'summary' => $this->newsNormalizer->normalizeText($i['summary']),
                'source' => self::SOURCE,
            ],
            $articles
        );

        return array_values($map);
    }

    private function normalizeDateTimeFromUnix(int $pubtime): string
    {
        $unixTime = (int) mb_substr((string) $pubtime, 0, -3);
        $dt = new DateTimeImmutable("@$unixTime");

        return $this->newsNormalizer->normalizeDateTime($dt);
    }
}
