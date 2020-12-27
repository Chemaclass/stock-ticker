<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class News extends AbstractWriteModel
{
    public const DATETIME = 'datetime';
    public const TIMEZONE = 'timezone';
    public const URL = 'url';
    public const TITLE = 'title';
    public const SUMMARY = 'summary';
    public const SOURCE = 'source';
    public const PUBLISHER = 'publisher';
    public const IMAGES = 'images';

    private const METADATA = [
        self::DATETIME => [
            'type' => self::TYPE_STRING,
        ],
        self::TIMEZONE => [
            'type' => self::TYPE_STRING,
        ],
        self::URL => [
            'type' => self::TYPE_STRING,
        ],
        self::TITLE => [
            'type' => self::TYPE_STRING,
        ],
        self::SUMMARY => [
            'type' => self::TYPE_STRING,
        ],
        self::PUBLISHER => [
            'type' => self::TYPE_STRING,
        ],
        self::IMAGES => [
            'type' => self::TYPE_ARRAY,
        ],
    ];

    protected ?string $datetime = null;
    protected ?string $timezone = null;
    protected ?string $url = null;
    protected ?string $title = null;
    protected ?string $summary = null;
    protected ?string $source = null;
    protected ?string $publisher = null;
    protected ?array $images = null;

    public function getDatetime(): ?string
    {
        return $this->datetime;
    }

    public function setDatetime(?string $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
