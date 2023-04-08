<?php

declare(strict_types=1);

namespace EugeneErg\Collections\DataTransferObjects;

class Item implements \JsonSerializable
{
    public function __construct(public readonly mixed $key, public readonly mixed $value)
    {
    }

    public static function fromArray(array $data): static
    {
        return new static($data['key'], $data['value']);
    }

    public function jsonSerialize(): array
    {
        return ['key' => $this->key, 'value' => $this->value];
    }
}