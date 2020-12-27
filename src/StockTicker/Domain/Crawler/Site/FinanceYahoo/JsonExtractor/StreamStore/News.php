<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor\StreamStore;

use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractorInterface;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizerInterface;
use DateTimeImmutable;

final class News implements JsonExtractorInterface
{
    private const SOURCE = 'FinanceYahoo';
    private const TYPE_ARTICLE = 'article';

    private NewsNormalizerInterface $newsNormalizer;

    public function __construct(NewsNormalizerInterface $newsNormalizer)
    {
        $this->newsNormalizer = $newsNormalizer;
    }

    public function extractFromJson(array $json): array
    {
        $streams = $json['context']['dispatcher']['stores']['StreamStore']['streams'];
        $first = reset($streams);
        $streamItems = $first['data']['stream_items'];

        $articles = $this->filterOnlyArticles($streamItems);
        $extractedInfo = $this->extractInfo($articles);
        $sorted = $this->sortNewestFirst($extractedInfo);

        return $this->newsNormalizer->limitByMaxToFetch($sorted);
    }

    private function filterOnlyArticles(array $items): array
    {
        return array_filter(
            $items,
            static fn (array $i): bool => self::TYPE_ARTICLE === $i['type']
        );
    }

    private function extractInfo(array $articles): array
    {
        $normalizedArticles = array_map(
            fn (array $article): array => $this->normalizeArticle($article),
            $articles
        );

        return array_values($normalizedArticles);
    }

    private function normalizeArticle(array $article): array
    {
        return [
            'source' => self::SOURCE,
            'datetime' => $this->normalizeDateTimeFromUnix($article['pubtime'] ?? 0),
            'timezone' => $this->newsNormalizer->getTimeZoneName(),
            'url' => $article['url'] ?? '',
            'title' => $this->newsNormalizer->normalizeText($article['title'] ?? ''),
            'summary' => $this->newsNormalizer->normalizeText($article['summary'] ?? ''),
            'publisher' => $article['publisher'] ?? '',
            'images' => $article['images'] ?? [],
        ];
    }

    private function normalizeDateTimeFromUnix(int $pubtime): string
    {
        $unixTime = (int) mb_substr((string) $pubtime, 0, -3);
        $dateTime = new DateTimeImmutable("@$unixTime");

        return $this->newsNormalizer->normalizeDateTime($dateTime);
    }

    private function sortNewestFirst(array $articles): array
    {
        usort(
            $articles,
            static fn (array $a, array $b) => $b['datetime'] <=> $a['datetime']
        );

        return $articles;
    }
}
