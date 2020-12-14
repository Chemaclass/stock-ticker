<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\Shared;

use DateTimeImmutable;
use DateTimeZone;

final class NewsNormalizer
{
    private const NORMALIZED_DATETIME_FORMAT = 'Y-m-d H:i:s';

    private const DEFAULT_MAX_TEXT_LENGTH_CHARS = 180;

    private DateTimeZone $timeZone;

    private ?int $maxNewsToFetch;

    private int $maxTextLengthChars;

    public function __construct(
        DateTimeZone $timeZone,
        ?int $maxNewsToFetch = null,
        int $maxTextLengthChars = self::DEFAULT_MAX_TEXT_LENGTH_CHARS
    ) {
        $this->timeZone = $timeZone;
        $this->maxNewsToFetch = $maxNewsToFetch;
        $this->maxTextLengthChars = $maxTextLengthChars;
    }

    public function normalizeDateTime(DateTimeImmutable $dt): string
    {
        $dt->setTimeZone($this->timeZone);

        return $dt->format(self::NORMALIZED_DATETIME_FORMAT);
    }

    public function getTimeZoneName(): string
    {
        return $this->timeZone->getName();
    }

    public function normalizeText(string $text): string
    {
        if (mb_strlen($text) < $this->maxTextLengthChars) {
            return $text;
        }

        return mb_substr($text, 0, $this->maxTextLengthChars) . '...';
    }

    public function limitByMaxToFetch(array $info): array
    {
        if (null === $this->maxNewsToFetch) {
            return $info;
        }

        return array_slice($info, 0, $this->maxNewsToFetch);
    }
}
