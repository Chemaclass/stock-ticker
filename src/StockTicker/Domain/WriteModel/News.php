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
        self::SOURCE => [
            'type' => self::TYPE_STRING,
        ],
    ];

    protected string $datetime = '';

    protected string $timezone = '';

    protected string $url = '';

    protected string $title = '';

    protected string $summary = '';

    protected string $source = '';

    public function getDatetime(): string
    {
        return $this->datetime;
    }

    public function setDatetime(string $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
