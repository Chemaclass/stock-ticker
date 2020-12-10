<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class Company
{
    /**
     * @var array<string, ExtractedFromJson>
     */
    private array $summary;

    public function __construct(array $summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return array<string, ExtractedFromJson>
     */
    public function summary(): array
    {
        return $this->summary;
    }
}
