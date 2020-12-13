<?php

declare(strict_types=1);


namespace Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlExtractor;


use Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlCrawlerInterface;
use DateTimeZone;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

final class News implements HtmlCrawlerInterface
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

    public function crawlHtml(string $html): array
    {
        $news = [];

        $nodes = (new Crawler($html))
            ->filter('#barrons-news-infinite ul li');

        foreach ($nodes as $node) {
            $news[] = $this->extractInfo($node);
        }

        return $news;
    }

    private function extractInfo(DOMNode $node): array
    {
        $html = $this->getInnerHtml($node);

        preg_match(
            '/^<span class="date">(?<date>.+)<\/span><a href="(?<url>.+)">(?<title>.+)<\/a>/',
            $html,
            $matches
        );

        return [
            'date' => $matches['date'],
            'url' => $matches['url'],
            'title' => $matches['title'],
        ];
    }

    private function getInnerHtml(DOMNode $node): string
    {
        $innerHTML = '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }

        return htmlspecialchars_decode($innerHTML);
    }
}