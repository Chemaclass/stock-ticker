<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class Company
{
    private Ticker $ticker;

    /**
     * @var array<string, ExtractedFromJson>
     */
    private array $summary;

    public function __construct(Ticker $ticker, array $summary)
    {
        $this->ticker = $ticker;
        $this->summary = $summary;
    }

    public function __toString(): string
    {
        return 'Ticker: ' . $this->ticker->symbol();
    }

    public function ticker(): Ticker
    {
        return $this->ticker;
    }

    /**
     * @return array<string, ExtractedFromJson>
     */
    public function summary(): array
    {
        return $this->summary;
    }
}
