<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class ExtractedFromJson
{
    private array $data;

    /**
     * @psalm-pure
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @psalm-pure
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @psalm-pure
     */
    public static function fromString(string $string): self
    {
        return new self([$string]);
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return reset($this->data);
    }

    /**
     * Gets the element with its original type
     *
     * @return array|bool|float|int|string
     */
    public function get(string $key = '')
    {
        if (!isset($this->data[$key]) && is_array(reset($this->data))) {
            return $this->data;
        }

        return $this->data[$key] ?? (string) $this;
    }
}
