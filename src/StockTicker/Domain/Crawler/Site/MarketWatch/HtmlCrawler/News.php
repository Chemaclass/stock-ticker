<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\MarketWatch\HtmlCrawler;

use Chemaclass\StockTicker\Domain\Crawler\Site\MarketWatch\HtmlCrawlerInterface;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizerInterface;
use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

final class News implements HtmlCrawlerInterface
{
    private const SOURCE = 'MarketWatch';

    private NewsNormalizerInterface $newsNormalizer;

    public function __construct(NewsNormalizerInterface $newsNormalizer)
    {
        $this->newsNormalizer = $newsNormalizer;
    }

    public function crawlHtml(string $html): array
    {
        $nodes = (new Crawler($html))
            ->filter('div[data-tab-pane="MarketWatch"] div div.element--article');

        $news = array_map(
            fn ($node) => $this->extractInfo($node),
            iterator_to_array($nodes),
        );

        return $this->newsNormalizer->limitByMaxToFetch($news);
    }

    private function extractInfo(DOMNode $node): array
    {
        $innerHtml = $this->innerHtml($node);

        if (mb_strpos($innerHtml, 'data-srcset=') !== false) {
            $match = '/<div data-timestamp="(?<timestamp>\d{10})(.|\n)*data-srcset=(?<image>.[^\?]*)(.|\n)*<a class="link" href="(?<url>.*)".*(?<title>(.|\n)*)<\/a>(.|\n)*<span class="article__author">by (?<author>.*)<\/span>/';
        } else {
            $match = '/<div data-timestamp="(?<timestamp>\d{10})(.|\n)*(.|\n)*<a class="link" href="(?<url>.*)".*(?<title>(.|\n)*)<\/a>(.|\n)*<span class="article__author">by (?<author>.*)<\/span>/';
        }

        preg_match(
            $match,
            $this->innerHtml($node),
            $matches,
        );

        return [
            'source' => self::SOURCE,
            'author' => $matches['author'] ?? 'Unknown author',
            'datetime' => $this->normalizeIncomingDate((int) ($matches['timestamp'] ?? 0)),
            'timezone' => $this->newsNormalizer->getTimeZoneName(),
            'url' => $matches['url'] ?? 'Unknown url',
            'title' => $this->newsNormalizer->normalizeText($this->normalizeTitle($matches['title'] ?? 'Unknown title', )),
            'images' => isset($matches['image']) ? [$matches['image']] : null,
        ];
    }

    private function innerHtml(DOMNode $node): string
    {
        $doc = new DOMDocument();
        $doc->appendChild($doc->importNode($node, true));

        return htmlspecialchars_decode(trim($doc->saveHTML()));
    }

    private function normalizeIncomingDate(int $timestamp): string
    {
        $dt = (new DateTimeImmutable())->setTimestamp($timestamp);

        return $this->newsNormalizer->normalizeDateTime($dt);
    }

    private function normalizeTitle(string $title): string
    {
        return trim(str_replace(['\n', '\r'], '', $title));
    }
}
