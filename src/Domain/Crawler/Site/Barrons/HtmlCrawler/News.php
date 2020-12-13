<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlCrawler;

use Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlCrawlerInterface;
use DateTimeImmutable;
use DateTimeZone;
use DOMNode;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

final class News implements HtmlCrawlerInterface
{
    private const DEFAULT_MAX_TEXT_LENGTH_CHARS = 180;

    private const NORMALIZED_DATETIME_FORMAT = 'Y-m-d H:i:s';

    private const DIFF_INCOMING_FORMATS = [
        11 => 'M d, Y', // 'Dec 9, 2020'
        12 => 'M d, Y', // 'Dec 13, 2020'
        17 => 'M d, Y H:i', // Dec 9, 2020 8:00
        18 => 'M d, Y H:i', // Dec 13, 2020 8:00
    ];

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

    public function crawlHtml(string $html): array
    {
        $news = [];

        $nodes = (new Crawler($html))
            ->filter('#barrons-news-infinite ul li');

        foreach ($nodes as $node) {
            $news[] = $this->extractInfo($node);
        }

        return $this->limitByMaxToFetch($news);
    }

    private function extractInfo(DOMNode $node): array
    {
        preg_match(
            '/^<span class="date">(?<date>.+)<\/span><a href="(?<url>.+)">(?<title>.+)<\/a>/',
            $this->innerHtml($node),
            $matches
        );

        return [
            'publicationDateTime' => $this->normalizeDateTime($matches['date']),
            'timezone' => $this->dateTimeZone->getName(),
            'url' => $matches['url'],
            'title' => $this->normalizeText($matches['title']),
            'summary' => '',
        ];
    }

    private function innerHtml(DOMNode $node): string
    {
        $innerHtml = '';

        foreach ($node->childNodes as $child) {
            if (null !== $child->ownerDocument) {
                $innerHtml .= $child->ownerDocument->saveXML($child);
            }
        }

        return htmlspecialchars_decode($innerHtml);
    }

    private function normalizeDateTime(string $incomingDate): string
    {
        $len = mb_strlen($incomingDate);
        $incomingFormat = self::DIFF_INCOMING_FORMATS[$len] ?? null;

        if (null === $incomingFormat) {
            throw new RuntimeException(sprintf('Format not found for the incomingDate: %s', $incomingDate));
        }

        $dt = DateTimeImmutable::createFromFormat($incomingFormat, $incomingDate);

        if (false === $dt) {
            throw new RuntimeException(sprintf(
                'Could not create a dateTime for incomingDate: "%s" to this format: "%s"',
                $incomingDate,
                $incomingDate
            ));
        }

        $dt->setTimeZone($this->dateTimeZone);

        return $dt->format(self::NORMALIZED_DATETIME_FORMAT);
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
