<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

use JsonSerializable;

use function array_key_exists;
use function in_array;

abstract class AbstractWriteModel implements JsonSerializable
{
    protected const TYPE_INT = 'int';
    protected const TYPE_FLOAT = 'float';
    protected const TYPE_STRING = 'string';
    protected const TYPE_ARRAY = 'array';

    protected const SCALAR_TYPES = [
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
        self::TYPE_ARRAY,
    ];

    protected const PROPERTY_NAME_MAP = [];

    /**
     * @return static
     */
    public function fromArray(array $data)
    {
        $props = get_object_vars($this);

        foreach ($data as $property => $value) {
            $propertyName = static::PROPERTY_NAME_MAP[$property] ?? $property;

            if (!array_key_exists($propertyName, $props)) {
                continue;
            }

            $concreteMeta = $this->metadata()[$propertyName] ?? [];
            $type = $concreteMeta['type'] ?? '';

            if (class_exists($type)) {
                $isArray = $concreteMeta['is_array'] ?? false;
                $this->$propertyName = ($isArray)
                    ? $this->mapValueAsArray($type, $value)
                    : $this->mapValueAsObject($type, $value);
            } elseif ($this->isScalar($type)) {
                $this->$propertyName = $value;
            }
        }

        return $this;
    }

    public function hasMandatoryFields(): bool
    {
        $props = get_object_vars($this);

        foreach ($props as $propertyName => $value) {
            $concreteMeta = $this->metadata()[$propertyName] ?? [];
            $type = $concreteMeta['type'] ?? '';

            if (class_exists($type)) {
                if (($concreteMeta['is_array'] ?? false)) {
                    foreach ($value as $item) {
                        if (!$item->hasMandatoryFields()) {
                            return false;
                        }
                    }
                } elseif ($value && !$value->hasMandatoryFields()) {
                    return false;
                }
            }

            $isMandatoryField = $concreteMeta['mandatory'] ?? false;

            if ($isMandatoryField && empty($value)) {
                return false;
            }
        }

        return true;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $result = [];
        $props = get_object_vars($this);

        foreach ($props as $name => $value) {
            $result[$name] = ($value instanceof self)
                ? $value->toArray()
                : $value;
        }

        return $result;
    }

    abstract protected function metadata(): array;

    private function mapValueAsArray(string $type, array $value): array
    {
        return array_map(
            static fn (array $i): self => (new $type())->fromArray($i),
            $value,
        );
    }

    private function mapValueAsObject(string $type, array $value): self
    {
        return (new $type())->fromArray($value);
    }

    private function isScalar(string $type): bool
    {
        return in_array($type, self::SCALAR_TYPES);
    }
}
