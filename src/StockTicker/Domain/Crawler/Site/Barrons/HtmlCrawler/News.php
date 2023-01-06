<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\HtmlCrawler;

use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\Exception\InvalidDateFormat;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\HtmlCrawlerInterface;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizerInterface;
use DateTimeImmutable;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

final class News implements HtmlCrawlerInterface
{
    private const SOURCE = 'Barrons';

    /**
     * TODO: Refactor this logic to use regex instead... Something like this:
     * (?<month>\w{3}) (?<day>\d{1,2}), (?<year>\d{4}) ?(?<time>)
     *
     * @var array<int, string> the key is the length of the incoming date,
     *                         the value is the mask-format that we can apply to it
     */
    private const DIFF_INCOMING_FORMATS = [
        11 => 'M d, Y',     // Dec 9, 2020
        12 => 'M d, Y',     // Dec 13, 2020
        17 => 'M d, Y H:i', // Dec 9, 2020 8:00
        18 => 'M d, Y H:i', // Dec 13, 2020 8:00
    ];

    private NewsNormalizerInterface $newsNormalizer;

    public function __construct(NewsNormalizerInterface $newsNormalizer)
    {
        $this->newsNormalizer = $newsNormalizer;
    }

    public function crawlHtml(string $html): array
    {
        $nodes = (new Crawler($html))
            ->filter('#barrons-news-infinite ul li');

        $news = array_map(
            fn ($node) => $this->extractInfo($node),
            iterator_to_array($nodes),
        );

        return $this->newsNormalizer->limitByMaxToFetch($news);
    }

    private function extractInfo(DOMNode $node): array
    {
        preg_match(
            '/^<span class="date">(?<date>.+)<\/span><a href="(?<url>.+)">(?<title>.+)<\/a>/',
            $this->innerHtml($node),
            $matches,
        );

        return [
            'source' => self::SOURCE,
            'datetime' => $this->normalizeIncomingDate($matches['date']),
            'timezone' => $this->newsNormalizer->getTimeZoneName(),
            'url' => $matches['url'],
            'title' => $this->newsNormalizer->normalizeText($matches['title']),
        ];
    }

    private function innerHtml(DOMNode $node): string
    {
        $innerHtml = '';

        foreach ($node->childNodes as $child) {
            if ($child->ownerDocument !== null) {
                $innerHtml .= $child->ownerDocument->saveXML($child);
            }
        }

        return htmlspecialchars_decode($innerHtml);
    }

    private function normalizeIncomingDate(string $incomingDate): string
    {
        $incomingDate = trim($incomingDate);

        if (mb_strlen($incomingDate) >= 25) {
            $incomingDate = mb_substr($incomingDate, 0, -8);
        }

        $len = mb_strlen($incomingDate);
        $incomingFormat = self::DIFF_INCOMING_FORMATS[$len] ?? null;

        if ($incomingFormat === null) {
            throw InvalidDateFormat::forIncomingDate($incomingDate);
        }

        $dt = DateTimeImmutable::createFromFormat($incomingFormat, $incomingDate);

        if ($dt === false) {
            throw InvalidDateFormat::couldNotCreateDateTime($incomingDate, $incomingFormat);
        }

        return $this->newsNormalizer->normalizeDateTime($dt);
    }
}
