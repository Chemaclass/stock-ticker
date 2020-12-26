<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class Quote extends AbstractWriteModel
{
    private const SYMBOL = 'symbol';
    private const COMPANY_NAME = 'companyName';
    private const REGULAR_MARKET_PRICE = 'regularMarketPrice';
    private const CURRENCY = 'currency';
    private const REGULAR_MARKET_CHANGE = 'regularMarketChange';
    private const REGULAR_MARKET_CHANGE_PERCENT = 'regularMarketChangePercent';
    private const MARKET_CAP = 'marketCap';
    private const URL = 'url';
    private const LAST_TREND = 'lastTrend';
    private const LATEST_NEWS = 'latestNews';

    private const METADATA = [
        self::SYMBOL => [
            'type' => self::TYPE_STRING,
        ],
        self::COMPANY_NAME => [
            'type' => CompanyName::class,
        ],
        self::REGULAR_MARKET_PRICE => [
            'type' => RegularMarketPrice::class,
        ],
        self::CURRENCY => [
            'type' => self::TYPE_STRING,
        ],
        self::REGULAR_MARKET_CHANGE => [
            'type' => RegularMarketChange::class,
        ],
        self::REGULAR_MARKET_CHANGE_PERCENT => [
            'type' => RegularMarketChangePercent::class,
        ],
        self::MARKET_CAP => [
            'type' => MarketCap::class,
        ],
        self::URL => [
            'type' => self::TYPE_STRING,
        ],
        self::LAST_TREND => [
            'type' => Trend::class,
            'is_array' => true,
        ],
        self::LATEST_NEWS => [
            'type' => News::class,
            'is_array' => true,
        ],
    ];

    protected ?string $symbol = null;

    protected ?CompanyName $companyName = null;

    protected ?string $currency = null;

    protected ?string $url = null;

    protected ?RegularMarketPrice $regularMarketPrice = null;

    protected ?RegularMarketChange $regularMarketChange = null;

    protected ?RegularMarketChangePercent $regularMarketChangePercent = null;

    protected ?MarketCap $marketCap = null;

    /** @var Trend[] */
    protected array $lastTrend = [];

    /** @var News[] */
    protected array $latestNews = [];

    public function getCompanyName(): ?CompanyName
    {
        return $this->companyName;
    }

    public function setCompanyName(CompanyName $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getRegularMarketPrice(): ?RegularMarketPrice
    {
        return $this->regularMarketPrice;
    }

    public function setRegularMarketPrice(RegularMarketPrice $regularMarketPrice): self
    {
        $this->regularMarketPrice = $regularMarketPrice;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRegularMarketChangePercent(): ?RegularMarketChangePercent
    {
        return $this->regularMarketChangePercent;
    }

    public function setRegularMarketChangePercent(RegularMarketChangePercent $regularMarketChangePercent): self
    {
        $this->regularMarketChangePercent = $regularMarketChangePercent;

        return $this;
    }

    public function getRegularMarketChange(): ?RegularMarketChange
    {
        return $this->regularMarketChange;
    }

    public function setRegularMarketChange(RegularMarketChange $changePercent): self
    {
        $this->regularMarketChange = $changePercent;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLastTrend(): ?array
    {
        return $this->lastTrend;
    }

    public function setLastTrend(array $lastTrend): self
    {
        $this->lastTrend = $lastTrend;

        return $this;
    }

    public function getMarketCap(): ?MarketCap
    {
        return $this->marketCap;
    }

    public function setMarketCap(?MarketCap $marketCap): self
    {
        $this->marketCap = $marketCap;

        return $this;
    }

    public function getLatestNews(): array
    {
        return $this->latestNews;
    }

    public function setLatestNews(array $latestNews): self
    {
        $this->latestNews = $latestNews;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
