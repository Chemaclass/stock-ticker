<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class CompanyName extends AbstractWriteModel
{
    private const SHORT_NAME = 'shortName';
    private const LONG_NAME = 'longName';

    private const METADATA = [
        self::SHORT_NAME => [
            'type' => self::TYPE_STRING,
        ],
        self::LONG_NAME => [
            'type' => self::TYPE_STRING,
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
