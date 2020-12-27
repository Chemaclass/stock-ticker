<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class Site
{
    /** @var array<string,mixed> */
    private array $crawled;

    public function __construct(array $crawled)
    {
        $this->crawled = $crawled;
    }

    public function crawled(): array
    {
        return $this->crawled;
    }
}
