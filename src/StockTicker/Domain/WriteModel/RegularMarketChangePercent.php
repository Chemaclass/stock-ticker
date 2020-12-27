<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class RegularMarketChangePercent extends AbstractWriteModel
{
    public const FMT = 'fmt';
    public const RAW = 'raw';

    private const METADATA = [
        self::FMT => [
            'type' => self::TYPE_STRING,
        ],
        self::RAW => [
            'type' => self::TYPE_FLOAT,
        ],
    ];

    protected ?string $fmt = null;
    protected ?float $raw = null;

    public function getFmt(): ?string
    {
        return $this->fmt;
    }

    public function setFmt(string $fmt): self
    {
        $this->fmt = $fmt;

        return $this;
    }

    public function getRaw(): ?float
    {
        return $this->raw;
    }

    public function setRaw(float $raw): self
    {
        $this->raw = $raw;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
