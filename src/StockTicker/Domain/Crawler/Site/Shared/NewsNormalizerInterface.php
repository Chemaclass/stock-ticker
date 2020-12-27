<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\Shared;

use DateTimeImmutable;

interface NewsNormalizerInterface
{
    public function normalizeDateTime(DateTimeImmutable $dt): string;

    public function getTimeZoneName(): string;

    public function normalizeText(string $text): string;

    public function limitByMaxToFetch(array $info): array;
}
