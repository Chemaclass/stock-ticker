<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

abstract class AbstractWriteModel
{
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_STRING = 'string';

    public const SCALAR_TYPES = [
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
    ];

    /**
     * @return static
     */
    public function fromArray(array $data)
    {
        $props = get_object_vars($this);

        foreach ($data as $propertyName => $value) {
            if (!array_key_exists($propertyName, $props)) {
                continue;
            }

            $meta = $this->metadata();
            $concreteMeta = $meta[$propertyName] ?? reset($meta);
            $type = $concreteMeta['type'];

            if ($this->isScalar($type)) {
                $this->$propertyName = $value;
            } elseif (class_exists($type)) {
                $isArray = $concreteMeta['is_array'] ?? false;

                $this->$propertyName = ($isArray)
                    ? $this->mapValueAsArray($type, $value)
                    : $this->mapValueAsObject($type, $value);
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        $result = [];
        $props = get_object_vars($this);

        foreach ($props as $propName => $propValue) {
            if ($propValue instanceof self) {
                $propValue = $propValue->toArray();
            }
            $result[$propName] = $propValue;
        }

        return $result;
    }

    abstract protected function metadata(): array;

    private function isScalar(string $type): bool
    {
        return in_array($type, self::SCALAR_TYPES);
    }

    private function mapValueAsArray(string $type, array $value): array
    {
        return array_map(
            static fn (array $i): self => (new $type())->fromArray($i),
            $value
        );
    }

    private function mapValueAsObject(string $type, array $value): self
    {
        return (new $type())->fromArray($value);
    }
}
