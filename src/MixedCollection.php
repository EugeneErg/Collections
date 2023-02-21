<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use ArrayIterator;
use EugeneErg\Collections\Traits\ImmutableTrait;
use EugeneErg\Collections\Traits\ValidateTrait;
use JetBrains\PhpStorm\Pure;
use LogicException;
use Traversable;

/**
 * @template T
 * @implements CollectionInterface<T>
 */
class MixedCollection implements CollectionInterface
{
    use ImmutableTrait;
    use ValidateTrait;

    public function __construct(protected array $items = [], bool $immutable = true)
    {
        $this->immutable = $immutable;
        self::validateItems($items);
    }

    /** @inheritDoc */
    public static function fromArray(array $items, bool $immutable = true): static
    {
        return new static($items, $immutable);
    }

    public static function fromInstance(CollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray($collection->toArray(), $immutable);
    }

    public static function fromFillKeys(
        ScalarCollectionInterface $collection,
        mixed $value,
        bool $immutable = true,
    ): static {
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
        return static::fromArray(array_column($collection->items, $columnKey, $indexKey));
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

    public static function fromMerge(CollectionInterface ...$collections): static
    {
        return static::fromArray(array_merge(...self::collectionsToArrays($collections)));
    }

    public static function fromReplace(CollectionInterface ...$collections): static
    {
        return static::fromArray(
            count($collections) === 0
                ? []
                : array_replace(...self::collectionsToArrays($collections)),
        );
    }

    public static function fromMap(callable $callback, CollectionInterface ...$collections): static
    {
        return static::fromArray(
            count($collections) === 0
                ? []
                : array_map($callback, ...static::collectionsToArrays($collections)),
        );
    }

    public static function fromDiff(
        bool|callable $value,
        bool|callable $key,
        CollectionInterface ...$collections,
    ): static {
        return self::fromIntersectOrDiff(true, $value, $key, ...$collections);
    }

    public static function fromIntersect(
        bool|callable $value,
        bool|callable $key,
        CollectionInterface ...$collections,
    ): static {
        return self::fromIntersectOrDiff(false, $value, $key, ...$collections);
    }

    public function reverse(bool $preserveKeys = false): static
    {
        return $this->setItemsWithoutValidate(array_reverse($this->items, $preserveKeys));
    }

    public function filter(callable $callback = null): static
    {
        return $this->setItemsWithoutValidate(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    public function slice(int $offset = 0, ?int $length = null, bool $preserveKeys = false): static
    {
        return $this->setItemsWithoutValidate(array_slice($this->items, $offset, $length, $preserveKeys));
    }

    public function splice(int $offset = 0, int $length = null, CollectionInterface $replacement = null): static
    {
        $this->checkMutable();
        $items = $replacement?->items ?? [];
        self::validateItems($items);
        $result = array_splice($this->items, $offset, $length, $items);

        return self::fromArray($result);
    }

    public function replaceKeys(ScalarCollectionInterface $collection): static
    {
        return $this->setItemsWithoutValidate(array_combine($collection->items, $this->items));
    }

    public function changeKeys(callable $callback): static
    {
        return $this->replaceKeys(ScalarCollection::fromMap($callback, StringCollection::fromKeys($this)));
    }

    public function setKeyCase(bool $toUpper = true): static
    {
        return $this->setItemsWithoutValidate(
            array_change_key_case($this->items, $toUpper ? CASE_UPPER : CASE_LOWER),
        );
    }

    /** @inheritDoc */
    public function set(mixed $value, int|string|null $key = null): static
    {
        $result = $this->getMutable();
        self::validate($value);
        $key === null
            ? $result->items[] = $value
            : $result->items[(string) $key] = $value;

        return $result;
    }

    public function unset(int|string $key): static
    {
        $result = $this->getMutable();
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
        self::validateItems($values);
        $result = $this->getMutable();
        array_push($result->items, ...$values);

        return $result;
    }

    /** @inheritDoc */
    public function unshift(mixed ...$values): static
    {
        self::validateItems($values);
        $result = $this->getMutable();
        array_unshift($result->items, ...$values);

        return $result;
    }

    public function toList(): static
    {
        return $this->setItemsWithoutValidate(array_values($this->items));
    }

    public function shuffle(): static
    {
        $result = $this->getMutable();
        shuffle($result->items);

        return $result;
    }

    public function sort(bool $asc = true, ?bool $withKeys = null, ?callable $callable = null): static
    {
        $result = $this->getMutable();

        if (!$asc && $callable !== null) {
            return $this->sort(true, $withKeys, fn (mixed $value1, mixed $value2): int => $callable($value2, $value1));
        }

        $method = ($callable === null ? '' : 'u') . match ($withKeys) {
            true => 'k',
            false => '',
            null => 'a',
        } . ($asc ? '' : 'r') . 'sort';
        $callable === null ? $method($result->items) : $method($result->items, $callable);

        return $result;
    }

    public function unique(): static
    {
        return $this->setItemsWithoutValidate(array_unique($this->items, SORT_REGULAR));
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
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

    /** @inheritDoc */
    public function offsetExists($offset): bool
    {
        return isset($this->items[(string) $offset]);
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
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
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

    /** @inheritDoc */
    public function offsetGet($offset): mixed
    {
        return $this->items[(string) $offset];
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

    public function randomKey(): int|string|null
    {
        return $this->isEmpty() ? null : array_rand($this->items);
    }

    /** @inheritDoc */
    public function search(mixed $needle, bool $strict = true): int|string|null
    {
        $result = array_search($needle, $this->items, $strict);

        return $result === false ? null : $result;
    }

    /** @inheritDoc */
    public function find(callable $needle): int|string|null
    {
        foreach ($this as $key => $value) {
            if ($needle($value)) {
                return $key;
            }
        }

        return null;
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

    /** @inheritDoc */
    public function toArray(): array
    {
        return $this->items;
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    protected function setItemsWithoutValidate(array $items): static
    {
        $result = $this->getMutable();
        $result->items = $items;

        return $result;
    }

    private function checkMutable(): void
    {
        if ($this->immutable) {
            throw new LogicException('Is immutable collection');
        }
    }

    private static function collectionsToArrays(array $collections): array
    {
        return array_map(fn (self $collection): array => $collection->items, $collections);
    }

    private static function fromIntersectOrDiff(
        bool $isDiff,
        bool|callable $value,
        bool|callable $key,
        CollectionInterface ...$collections,
    ): static {
        if (count($collections) === 0 || ($value === false && $key === false && !$isDiff)) {
            return static::fromArray([]);
        }

        if ($value === false && $key === false) {
            return static::fromInstance(end($collections));
        }

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

        return static::fromArray($method(...$arguments));
    }
}
