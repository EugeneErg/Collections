<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use ArrayIterator;
use EugeneErg\Collections\Traits\ImmutableTrait;
use EugeneErg\Collections\Traits\ValidateTrait;
use InvalidArgumentException;
use LogicException;
use Traversable;

/**
 * @implements CollectionInterface<mixed>
 */
class MixedCollection implements CollectionInterface
{
    use ImmutableTrait {
        getMutable as protected;
    }
    use ValidateTrait;

    protected const VALUE_TYPE = null;
    protected const KEY_TYPE = null;

    public function __construct(protected array $items = [], bool $immutable = true)
    {
        self::validateItems($items);
        $this->immutable = $immutable;
    }

    public static function fromArray(array $items = [], bool $immutable = true): static
    {
        return new static($items, $immutable);
    }

    public static function fromMixedArray(array $items, callable $callback, bool $immutable = true): static
    {
        return static::fromWalk(self::fromArray($items), $callback, $immutable);
    }

    public static function fromCombine(
        CollectionInterface $keys,
        CollectionInterface $values,
        bool $immutable = true,
    ): static {
        return static::fromArray(array_combine($keys->toArray(), $values->toArray()), $immutable);
    }

    public static function fromInstance(CollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray($collection->toArray(), $immutable);
    }

    public static function fromFillKeys(
        CollectionInterface $collection,
        mixed $value,
        bool $immutable = true,
    ): static {
        return static::fromArray(array_fill_keys($collection->toArray(), $value), $immutable);
    }

    public static function fromFill(int $start, int $count, mixed $value, bool $immutable = true): static
    {
        return static::fromArray(array_fill($start, $count, $value), $immutable);
    }

    public static function fromColumn(
        CollectionInterface $collection,
        string|callable $valueColumn = null,
        string|callable $keyColumn = null,
        bool $immutable = true,
    ): static {
        if (!is_callable($valueColumn) && !is_callable($keyColumn)) {
            return static::fromArray(array_column($collection->toArray(), $valueColumn, $keyColumn));
        }

        if (!is_callable($keyColumn)) {
            return static::fromWalk($collection, $valueColumn, $immutable);
        }

        if (!is_callable($valueColumn) && $valueColumn !== null) {
            $valueColumn = fn (mixed $value) => array_column([$value], $valueColumn)[0];
        }

        $result = [];

        foreach ($collection as $key => $value) {
            $result[$keyColumn($value, $key)] = $valueColumn === null ? $value : $valueColumn($value, $key);
        }

        return static::fromArray($result);
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

    public static function fromKeys(CollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray(array_keys($collection->toArray()), $immutable);
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

    public function replaceKeys(CollectionInterface $collection): static
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

    public function set(mixed $value, mixed $key = null): static
    {
        $result = $this->getMutable();
        self::validate($value);
        $key === null
            ? $result->items[] = $value
            : $result->items[(string) $key] = $value;

        return $result;
    }

    public function unset(mixed $key): static
    {
        $result = $this->getMutable();
        unset($result->items[$key]);

        return $result;
    }

    public function fill(int $length, mixed $value): static
    {
        return $this->setItemsWithoutValidate(array_pad($this->items, $length, $value));
    }

    public function push(CollectionInterface $collection): static
    {
        $values = self::fromValues($collection)->toArray();
        self::validateItems($values);
        $result = $this->getMutable();
        array_push($result->items, ...$values);

        return $result;
    }

    public function unshift(CollectionInterface $collection): static
    {
        $values = self::fromValues($collection)->toArray();
        self::validateItems($values);
        $result = $this->getMutable();
        array_unshift($result->items, ...$values);

        return $result;
    }

    public static function fromValues(CollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray(array_values($collection->toArray()), $immutable);
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

    public function unique(bool $immutable = true): static
    {
        return static::fromArray(array_unique($this->toArray(), SORT_REGULAR), immutable: $immutable);
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function isList(): bool
    {
        return array_is_list($this->items);
    }

    public function keyExists(mixed $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function equals(CollectionInterface $collection, bool $strict = false): bool
    {
        return get_class($collection) === static::class
            && $strict ? $this->items === $collection->items : $this->items == $collection->items;
    }

    public function has(mixed $value, bool $strict = false): bool
    {
        return in_array($value, $this->items, $strict);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[(string) $offset]);
    }

    /** @inheritDoc */
    public function first(): mixed
    {
        $key = $this->firstKey();

        return $key === null ? null : $this->items[$key];
    }

    public function last(): mixed
    {
        $key = $this->lastKey();

        return $key === null ? null : $this->items[$key];
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function shift(): mixed
    {
        $this->checkMutable();

        return array_shift($this->items);
    }

    public function pop(): mixed
    {
        $this->checkMutable();

        return array_pop($this->items);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[(string) $offset];
    }

    public function keyByPosition(int $position): mixed
    {
        return array_key_first(array_slice($this->items, $position, 1, true));
    }

    public function firstKey(): mixed
    {
        return array_key_first($this->items);
    }

    public function lastKey(): mixed
    {
        return array_key_last($this->items);
    }

    public function randomKey(): mixed
    {
        return $this->isEmpty() ? null : array_rand($this->items);
    }

    public function search(mixed $value, bool $strict = true): mixed
    {
        /** @var mixed $result */
        $result = array_search($value, $this->items, $strict);

        return $result === false ? null : $result;
    }

    public function find(callable $needle): mixed
    {
        foreach ($this as $key => $value) {
            if ($needle($value)) {
                return $key;
            }
        }

        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->checkMutable();
        $this->set($value, $offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->checkMutable();
        $this->unset($offset);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function count(): int
    {
        return count($this->items);
    }

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
        return array_map(fn (CollectionInterface $collection): array => $collection->toArray(), $collections);
    }

    /**
     * function        value key
     * array_*_assoc   true  true
     * array_*_key     false true
     * array_*_uassoc  true  func
     * array_*_ukey    false func
     * array_*         true  false
     * array_u*_assoc  func  true
     * array_u*_uassoc func  func
     * array_u*        func  false
     */
    private static function fromIntersectOrDiff(
        bool $isDiff,
        bool|callable $value,
        bool|callable $key,
        CollectionInterface ...$collections,
    ): static {
        if (count($collections) === 0 || ($value === false && $key === false && !$isDiff)) {
            return static::fromArray();
        }

        if ($value === false && $key === false) {
            return static::fromInstance(end($collections));
        }

        if ($value === true) {
            $value = fn(mixed $valueA, mixed $valueB): int => $valueA <=> $valueB;
        }

        $arguments = self::collectionsToArrays($collections);

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

    public static function isValidValue(mixed $value): bool
    {
        return static::isValidItems([$value], static::VALUE_TYPE);
    }

    public static function isValidKey(mixed $key): bool
    {
        return static::isValidItems([$key], static::KEY_TYPE);
    }

    private static function validate(mixed $item): void
    {
        if (!self::isValidValue($item)) {
            throw new InvalidArgumentException('Invalid item');
        }
    }

    protected static function validateItems(
        array $values,
        callable|string|null $valueType = null,
        callable|string|null $keyType = null,
    ): void {
        if (!self::isValidItems($values, $valueType, $keyType)) {
            throw new InvalidArgumentException('Invalid items');
        }
    }
}
