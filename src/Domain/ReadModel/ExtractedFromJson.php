<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\ReadModel;

/**
 * @psalm-immutable
 */
final class ExtractedFromJson
{
    private array $data;

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

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
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return (string) $this;
    }
}