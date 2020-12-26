<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class RegularMarketChange extends AbstractWriteModel
{
    private const RAW = 'raw';
    private const FMT = 'fmt';

    private const METADATA = [
        self::FMT => [
            'type' => self::TYPE_STRING,
        ],
        self::RAW => [
            'type' => self::TYPE_FLOAT,
        ],
    ];

    protected ?float $raw = null;

    protected ?string $fmt = null;

    public function getRaw(): ?float
    {
        return $this->raw;
    }

    public function setRaw(?float $raw): self
    {
        $this->raw = $raw;

        return $this;
    }

    public function getFmt(): ?string
    {
        return $this->fmt;
    }

    public function setFmt(?string $fmt): self
    {
        $this->fmt = $fmt;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
