<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class MarketCap extends AbstractWriteModel
{
    private const RAW = 'raw';
    private const FMT = 'fmt';
    private const LONG_FMT = 'longFmt';

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

    private ?float $raw = null;

    private ?string $fmt = null;

    private ?string $longFmt = null;

    public function fromArray(array $data): self
    {
        foreach ($data as $propertyName => $value) {
            switch ($propertyName) {
                case 'raw':
                case 'fmt':
                case 'longFmt':
                    $this->$propertyName = $value;

                    break;
            }
        }

        return $this;
    }

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
