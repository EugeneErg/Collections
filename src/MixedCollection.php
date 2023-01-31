<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use ArrayIterator;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use LogicException;

/**
 * @template T
 * @implements CollectionInterface<T>
 */
class MixedCollection implements CollectionInterface
{
    protected const ITEM_TYPE = null;

    public function __construct(protected array $items = [], private bool $immutable = true)
    {
        $this->validateItems($items);
    }

    /** @inheritDoc */
    public static function fromArray(array $items, bool $immutable = true): static
    {
        return new static($items, $immutable);
    }

    public static function fromMerge(bool $immutable, CollectionInterface ...$collections): static
    {
        return static::fromArray(array_merge(...self::collectionsToArrays($collections)), $immutable);
    }

    public static function fromMap(bool $immutable, callable $callback, CollectionInterface ...$collections): static
    {
        return static::fromArray(
            count($collections) === 0
                ? []
                : array_map($callback, ...static::collectionsToArrays($collections)),
            $immutable,
        );
    }

    public static function fromDiff(
        bool $immutable,
        bool|callable $value,
        bool|callable $key,
        CollectionInterface ...$collections,
    ): static {
        return self::fromIntersectOrDiff($immutable, true, $value, $key, ...$collections);
    }

    public static function fromIntersect(
        bool $immutable,
        bool|callable $value,
        bool|callable $key,
        CollectionInterface ...$collections,
    ): static {
        return self::fromIntersectOrDiff($immutable, false, $value, $key, ...$collections);
    }

    public static function fromFillKeys(ScalarCollectionInterface $collection, mixed $value, bool $immutable = true): static
    {
        return static::fromArray(array_fill_keys($collection->items, $value), $immutable);
    }

    /** @inheritDoc */
    public static function fromFill(int $startIndex, int $count, mixed $value, bool $immutable = true): static
    {
        return static::fromArray(array_fill($startIndex, $count, $value), $immutable);
    }

    public static function fromColumn(
        ObjectCollectionInterface $collection,
        string $columnKey,
        string $indexKey = null,
        bool $immutable = true,
    ): static {
        return static::fromArray(array_column($collection->items, $columnKey, $indexKey), $immutable);
    }

    public static function fromReplace(bool $immutable, CollectionInterface ...$collections): static
    {
        return static::fromArray(
            count($collections) === 0
                ? []
                : array_replace(...self::collectionsToArrays($collections)),
            $immutable,
        );
    }

    public static function fromInstance(CollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray($collection->toArray(), $immutable);
    }

    public function changeKeys(ScalarCollection $collection, bool $immutable = true): static
    {
        return $this->setItemsWithoutValidate(array_combine($collection->items, $this->items));
    }

    public function changeKeyCase(bool $toUpper = true): static
    {
        return $this->setItemsWithoutValidate(array_change_key_case($this->items, $toUpper ? CASE_UPPER : CASE_LOWER));
    }

    public static function fromWalk(CollectionInterface $collection, callable $callback, bool $immutable = true): static
    {
        $items = $collection->toArray();
        array_walk($items, function (&$value, $key) use ($callback): void {
            $value = $callback($value, $key);
        });

        return static::fromArray($items, $immutable);
    }

    public static function fromWalkRecursive(
        CollectionCollectionInterface $collection,
        callable $callback,
        bool $immutable = true,
    ): static {
        $items = $collection->toArrayRecursive();
        array_walk_recursive($items, function (&$value, $key) use ($callback): void {
            $value = $callback($value, $key);
        });

        return static::fromArray($items, $immutable);
    }

    /** @inheritDoc */
    public function isValidItem(mixed $item): bool
    {
        if (static::ITEM_TYPE === null) {
            return true;
        }

        if (is_callable(static::ITEM_TYPE)) {
            return (static::ITEM_TYPE)($item);
        }

        $type = static::ITEM_TYPE;

        return $item instanceof $type;
    }

    /** @inheritDoc */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function keyByPosition(int $position): int|string|null
    {
        return array_key_first(array_slice($this->items, $position, 1, true));
    }

    public function firstKey(): int|string|null
    {
        return array_key_first($this->items);
    }

    public function lastKey(): int|string|null
    {
        return array_key_last($this->items);
    }

    #[Pure] public function randomKey(): int|string|null
    {
        return $this->isEmpty() ? null : array_rand($this->items);
    }

    /** @inheritDoc */
    public function search(mixed $needle, bool $strict = true): int|string|null
    {
        $result = array_search($needle, $this->items, $strict);

        return $result === false ? null : $result;
    }

    #[Pure] public function first(): mixed
    {
        $key = $this->firstKey();

        return $key === null ? null : $this->items[$key];
    }

    #[Pure] public function last(): mixed
    {
        $key = $this->lastKey();

        return $key === null ? null : $this->items[$key];
    }

    /** @inheritDoc */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }

    /** @inheritDoc */
    public function toArray(): array
    {
        return $this->items;
    }

    /** @inheritDoc */
    public function offsetExists($offset): bool
    {
        return isset($this->items[(string) $offset]);
    }

    /** @inheritDoc */
    public function offsetGet($offset): mixed
    {
        return $this->items[(string) $offset];
    }

    /** @inheritDoc */
    public function offsetSet($offset, $value): void
    {
        $this->checkMutable();
        $this->set($value, $offset);
    }

    /** @inheritDoc */
    public function offsetUnset($offset): void
    {
        $this->checkMutable();
        $this->unset($offset);
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    public function isList(): bool
    {
        return array_is_list($this->items);
    }

    public function keyExists(string|int $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function equals(CollectionInterface $collection): bool
    {
        return get_class($collection) === static::class
            && $this->items === $collection->items;
    }

    /** @inheritDoc */
    public function has(mixed $needle, bool $strict = false): bool
    {
        return in_array($needle, $this->items, $strict);
    }

    public function reverse(bool $preserveKeys = false): static
    {
        return $this->setItemsWithoutValidate(array_reverse($this->items, $preserveKeys));
    }

    public function filter(callable $callback = null): static
    {
        return $this->setItemsWithoutValidate(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /** @inheritDoc */
    public function shift(): mixed
    {
        $this->checkMutable();
        return array_shift($this->items);
    }

    /** @inheritDoc */
    public function pop(): mixed
    {
        $this->checkMutable();
        return array_pop($this->items);
    }

    public function splice(int $offset = 0, int $length = null, CollectionInterface $replacement = null): static
    {
        $this->checkMutable();
        $items = $replacement?->items ?? [];
        $this->validateItems($items);
        $result = array_splice($this->items, $offset, $length, $items);

        return self::fromArray($result);
    }

    public function slice(int $offset = 0, ?int $length = null, bool $preserveKeys = false): static
    {
        return $this->setItemsWithoutValidate(array_slice($this->items, $offset, $length, $preserveKeys));
    }

    /** @inheritDoc */
    public function set(mixed $value, int|string|null $key = null): static
    {
        $result = $this->getMutableCollection();
        $this->validate($value);
        $key === null
            ? $result->items[] = $value
            : $result->items[(string) $key] = $value;

        return $result;
    }

    public function unset(int|string $key): static
    {
        $result = $this->getMutableCollection();
        unset($result->items[$key]);

        return $result;
    }

    /** @inheritDoc */
    public function fill(int $length, mixed $value): static
    {
        return $this->setItemsWithoutValidate(array_pad($this->items, $length, $value));
    }

    /** @inheritDoc */
    public function push(mixed ...$values): static
    {
        $this->validateItems($values);
        $result = $this->getMutableCollection();
        array_push($result->items, ...$values);

        return $result;
    }

    /** @inheritDoc */
    public function unshift(mixed ...$values): static
    {
        $this->validateItems($values);
        $result = $this->getMutableCollection();
        array_unshift($result->items, ...$values);

        return $result;
    }

    public function values(): static
    {
        return $this->setItemsWithoutValidate(array_values($this->items));
    }

    public function shuffle(): static
    {
        $result = $this->getMutableCollection();
        shuffle($result->items);

        return $result;
    }

    public function sort(bool $asc = true, ?bool $withKeys = null, ?callable $callable = null): static
    {
        $result = $this->getMutableCollection();

        if ($callable === null) {
            if ($withKeys === null) {
                $asc ? asort($result->items) : arsort($result->items);
            } elseif ($withKeys === true) {
                $asc ? ksort($result->items) : krsort($result->items);
            } else {
                $asc ? sort($result->items) : rsort($result->items);
            }
        } else {
            if (!$asc) {
                $callable = fn (mixed $value1, mixed $value2): int => - $callable($value1, $value2);
            }

            if ($withKeys === null) {
                uasort($result->items, $callable);
            } elseif ($withKeys === true) {
                uksort($result->items, $callable);
            } else {
                usort($result->items, $callable);
            }
        }

        return $result;
    }

    public function unique(): static
    {
        return $this->setItemsWithoutValidate(array_unique($this->items, SORT_REGULAR));
    }

    public function setImmutable(bool $immutable = true): static
    {
        $result = $immutable === $this->immutable ? $this : $this->getMutableCollection();
        $result->immutable = $immutable;

        return $result;
    }

    private function checkMutable(): void
    {
        if ($this->immutable) {
            throw new LogicException('Is immutable collection');
        }
    }

    protected function setItemsWithoutValidate(array $items): static
    {
        $result = $this->getMutableCollection();
        $result->items = $items;

        return $result;
    }

    protected function getMutableCollection(): static
    {
        return $this->immutable ? clone $this : $this;
    }

    private static function collectionsToArrays(array $collections): array
    {
        return array_map(fn (self $collection): array => $collection->items, $collections);
    }

    private function validate(mixed $item): void
    {
        if (!$this->isValidItem($item)) {
            throw new InvalidArgumentException('Invalid item');
        }
    }

    private function validateItems(array $items): void
    {
        foreach ($items as $item) {
            $this->validate($item);
        }
    }

    private static function fromIntersectOrDiff(
        bool $immutable,
        bool $isDiff,
        bool|callable $value,
        bool|callable $key,
        CollectionInterface ...$collections,
    ): static {
        $arguments = self::collectionsToArrays($collections);

        if ($value === true) {
            $value = fn(mixed $valueA, mixed $valueB): int => $valueA <=> $valueB;
        }

        if (is_callable($value)) {
            $arguments[] = $value;
        }

        if (is_callable($key)) {
            $arguments[] = $key;
        }

        $method = implode('_', array_filter([
            'array',
            (is_callable($value) ? 'u' : '') . ($isDiff ? 'diff' : 'intersect'),
            (is_callable($key) ? 'u' : '') . ($key === false ? '' : ($value === false ? 'key' : 'assoc')),
        ], fn (string $part): bool => $part !== ''));

        return static::fromArray($method(...$arguments), $immutable);
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
