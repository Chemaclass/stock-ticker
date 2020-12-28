<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class Trend extends AbstractWriteModel
{
    public const PERIOD = 'period';
    public const STRONG_BUY = 'strongBuy';
    public const BUY = 'buy';
    public const HOLD = 'hold';
    public const SELL = 'sell';
    public const STRONG_SELL = 'strongSell';

    protected const PROPERTY_NAME_MAP = [
        'period' => self::PERIOD,
        'Period' => self::PERIOD,
        'strong_buy' => self::STRONG_BUY,
        'strongBuy' => self::STRONG_BUY,
        'StrongBuy' => self::STRONG_BUY,
        'buy' => self::BUY,
        'Buy' => self::BUY,
        'hold' => self::HOLD,
        'Hold' => self::HOLD,
        'sell' => self::SELL,
        'Sell' => self::SELL,
        'strong_sell' => self::STRONG_SELL,
        'strongSell' => self::STRONG_SELL,
        'StrongSell' => self::STRONG_SELL,
    ];

    private const METADATA = [
        self::PERIOD => [
            'type' => self::TYPE_STRING,
        ],
        self::STRONG_BUY => [
            'type' => self::TYPE_INT,
        ],
        self::BUY => [
            'type' => self::TYPE_INT,
        ],
        self::HOLD => [
            'type' => self::TYPE_INT,
        ],
        self::SELL => [
            'type' => self::TYPE_INT,
        ],
        self::STRONG_SELL => [
            'type' => self::TYPE_INT,
        ],
    ];

    protected ?string $period = null;
    protected ?int $strongBuy = null;
    protected ?int $buy = null;
    protected ?int $hold = null;
    protected ?int $sell = null;
    protected ?int $strongSell = null;

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getStrongBuy(): ?int
    {
        return $this->strongBuy;
    }

    public function setStrongBuy(int $strongBuy): self
    {
        $this->strongBuy = $strongBuy;

        return $this;
    }

    public function getBuy(): ?int
    {
        return $this->buy;
    }

    public function setBuy(int $buy): self
    {
        $this->buy = $buy;

        return $this;
    }

    public function getHold(): ?int
    {
        return $this->hold;
    }

    public function setHold(int $hold): self
    {
        $this->hold = $hold;

        return $this;
    }

    public function getSell(): ?int
    {
        return $this->sell;
    }

    public function setSell(int $sell): self
    {
        $this->sell = $sell;

        return $this;
    }

    public function getStrongSell(): ?int
    {
        return $this->strongSell;
    }

    public function setStrongSell(int $strongSell): self
    {
        $this->strongSell = $strongSell;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
