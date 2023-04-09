<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\DataTransferObjects\Item;
use Traversable;

/**
 * @implements CollectionInterface<Item>
 * @implements ObjectCollection<Item>
 */
class ItemCollection extends ObjectCollection
{
    /** @psalm-var class-string<Item> */
    protected const VALUE_TYPE = Item::class;
    protected const STRICT = true;

    protected static function getNextKey(self $collection): mixed
    {
        $key = $collection->reverse()->find(fn (Item $item): bool => is_int($item->key));

        return $key === null ? 0 : $key + 1;
    }

    /** @inheritDoc */
    public function set(mixed $value, mixed $key = null): static
    {
        $result = $this->getMutable();
        self::validateItems([$value], static::VALUE_TYPE);
        self::validateItems([$key], static::KEY_TYPE);
        $key === null
            ? $key = static::getNextKey(clone $this)
            : $realKey = $this->getOffsetByKey($key);
        isset($realKey)
            ? $result->items[$realKey] = new Item($key, $value)
            : $result->items[] = new Item($key, $value);

        return $result;
    }

    public function unset(mixed $key): static
    {
        $result = $this->getMutable();
        $realKey = $this->getOffsetByKey($key);

        if ($realKey !== null) {
            unset($result->items[$realKey]);
        }

        return $result;
    }

    public function keyExists(mixed $key): bool
    {
        return $this->getOffsetByKey($key) !== null;
    }

    /** @inheritDoc */
    public function has(mixed $value, bool $strict = false): bool
    {
        return $this->search($value) !== null;
    }

    /** @inheritDoc */
    public function offsetExists(mixed $offset): bool
    {
        return $this->getItemByOffset($this->getOffsetByKey($offset))?->value !== null;
    }

    public function first(): mixed
    {
        return parent::first()?->value;
    }

    public function last(): mixed
    {
        return parent::last()?->value;
    }

    /** @inheritDoc */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return parent::reduce(fn (mixed $value, Item $item): mixed => $callback($value, $item->value));
    }

    /** @inheritDoc */
    public function shift(): mixed
    {
        return parent::shift()?->value;
    }

    /** @inheritDoc */
    public function pop(): mixed
    {
        return parent::pop()?->value;
    }

    /** @inheritDoc */
    public function offsetGet(mixed $offset): mixed
    {
        $realKey = $this->getOffsetByKey($offset);

        if ($realKey === null) {
            throw new \InvalidArgumentException();
        }

        return $this->getItemByOffset($offset)->value;
    }

    public function keyByPosition(int $position): mixed
    {
        return $this->getItemByOffset(parent::keyByPosition($position))?->key;
    }

    public function firstKey(): mixed
    {
        return $this->getItemByOffset(parent::firstKey())?->key;
    }

    public function lastKey(): mixed
    {
        return $this->getItemByOffset(parent::lastKey())?->key;
    }

    public function randomKey(): mixed
    {
        return $this->getItemByOffset(parent::randomKey())?->key;
    }

    /** @inheritDoc */
    public function search(mixed $value, bool $strict = true): mixed
    {
        $result = $this->find(
            $strict
                ? fn (Item $item): bool => $item->value === $value
                : fn (Item $item): bool => $item->value == $value,
        );

        return $result === null ? null : $this->items[$result]->key;
    }

    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        /** @var Item $item */
        foreach ($this->toArray() as $item) {
            yield $item->key => $item->value;
        }
    }

    private function getOffsetByKey(mixed $key): ?string
    {
        $result = $this->find(
            static::STRICT
                ? fn (Item $item): bool => $item->key === $key
                : fn (Item $item): bool => $item->key == $key,
        );

        return $result === null ? null : (string) $result;
    }

    private function getItemByOffset(?string $offset): ?Item
    {
        return $offset === null ? null : $this->toArray()[$offset];
    }
}
