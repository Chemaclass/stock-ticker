<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\ReadModel;

/** @psalm-immutable */
final class Summary
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getKeys(): array
    {
        return array_keys($this->data);
    }

    public function getFullSummary(): array
    {
        return $this->data;
    }
}
