<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\ReadModel;

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

    public function asArray(): array
    {
        return $this->data;
    }
}
