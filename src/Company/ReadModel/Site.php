<?php

declare(strict_types=1);

namespace App\Company\ReadModel;

/** @psalm-immutable */
final class Site
{
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
