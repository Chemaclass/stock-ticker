<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class MarketCap extends AbstractWriteModel
{
    public const RAW = 'raw';
    public const FMT = 'fmt';
    public const LONG_FMT = 'longFmt';

    protected const PROPERTY_NAME_MAP = [
        'raw' => self::RAW,
        'Raw' => self::RAW,
        'fmt' => self::FMT,
        'Fmt' => self::FMT,
        'long_fmt' => self::LONG_FMT,
        'longFmt' => self::LONG_FMT,
        'LongFmt' => self::LONG_FMT,
    ];

    private const METADATA = [
        self::RAW => [
            'type' => self::TYPE_FLOAT,
        ],
        self::FMT => [
            'type' => self::TYPE_STRING,
        ],
        self::LONG_FMT => [
            'type' => self::TYPE_STRING,
        ],
    ];

    protected ?float $raw = null;
    protected ?string $fmt = null;
    protected ?string $longFmt = null;

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

    public function getLongFmt(): ?string
    {
        return $this->longFmt;
    }

    public function setLongFmt(?string $longFmt): self
    {
        $this->longFmt = $longFmt;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
