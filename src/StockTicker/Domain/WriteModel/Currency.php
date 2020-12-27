<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class Currency extends AbstractWriteModel
{
    public const CURRENCY = 'currency';
    public const SYMBOL = 'symbol';

    private const METADATA = [
        self::CURRENCY => [
            'type' => self::TYPE_STRING,
        ],
        self::SYMBOL => [
            'type' => self::TYPE_STRING,
        ],
    ];

    protected ?string $currency = null;
    protected ?string $symbol = null;

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
