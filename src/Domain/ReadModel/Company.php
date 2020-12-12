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
    private array $info;

    /**
     * @psalm-pure
     */
    public static function empty(): self
    {
        return new self(Ticker::empty(), []);
    }

    public function __construct(Ticker $ticker, array $info)
    {
        $this->ticker = $ticker;
        $this->info = $info;
    }

    public function __toString(): string
    {
        return sprintf('Ticker: %s', $this->ticker->symbol());
    }

    public function ticker(): Ticker
    {
        return $this->ticker;
    }

    public function info(string $key): ExtractedFromJson
    {
        return $this->info[$key] ?? ExtractedFromJson::empty();
    }

    public function allInfo(): array
    {
        return $this->info;
    }
}
