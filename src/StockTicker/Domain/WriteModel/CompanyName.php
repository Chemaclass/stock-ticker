<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class CompanyName extends AbstractWriteModel
{
    public const SHORT_NAME = 'shortName';
    public const LONG_NAME = 'longName';

    protected const PROPERTY_NAME_MAP = [
        'short_name' => self::SHORT_NAME,
        'shortName' => self::SHORT_NAME,
        'ShortName' => self::SHORT_NAME,
        'long_name' => self::LONG_NAME,
        'longName' => self::LONG_NAME,
        'LongName' => self::LONG_NAME,
    ];

    private const METADATA = [
        self::SHORT_NAME => [
            'type' => self::TYPE_STRING,
        ],
        self::LONG_NAME => [
            'type' => self::TYPE_STRING,
            'mandatory' => true,
        ],
    ];

    protected ?string $shortName = null;
    protected ?string $longName = null;

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getLongName(): ?string
    {
        return $this->longName;
    }

    public function setLongName(?string $longName): self
    {
        $this->longName = $longName;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
