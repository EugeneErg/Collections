<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use ArrayAccess;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * @template TValue
 * @template TKey
 * @extends ArrayAccess<TKey, TValue>
 * @extends IteratorAggregate<TKey, TValue>
 */
interface CollectionInterface extends IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @param array<TKey, TValue> $items */
    public static function fromArray(array $items = [], bool $immutable = true): static;
    public static function fromMixedArray(array $items, callable $callback, bool $immutable = true): static;
    public static function fromInstance(self $collection, bool $immutable = true): static;
    /** @param TValue $value */
    public static function fromFillKeys(self $collection, mixed $value, bool $immutable = true): static;
    /** @param TValue $value */
    public static function fromFill(int $start, int $count, mixed $value, bool $immutable = true): static;
    public static function fromColumn(
        ObjectCollectionInterface $collection,
        string $valueColumn,
        string $keyColumn = null,
        bool $immutable = true,
    ): static;
    public static function fromWalk(self $collection, callable $callback, bool $immutable = true): static;
    public static function fromWalkRecursive(
        CollectionCollectionInterface $collection,
        callable $callback,
        bool $immutable = true,
    ): static;

    public static function fromMerge(self ...$collections): static;
    public static function fromReplace(self ...$collections): static;
    public static function fromMap(callable $callback, self ...$collections): static;
    public static function fromDiff(
        bool|callable $value,
        bool|callable $key,
        self ...$collections,
    ): static;
    public static function fromIntersect(
        bool|callable $value,
        bool|callable $key,
        self ...$collections,
    ): static;
    public static function fromValues(self $collection, bool $immutable = true): static;
    public static function fromKeys(self $collection, bool $immutable): static;
    public static function fromCombine(self $keys, self $values, bool $immutable = true): static;

    public function reverse(bool $preserveKeys = false): static;
    public function filter(callable $callback = null): static;
    public function slice(): static;
    public function splice(int $offset = 0, int $length = null, self $replacement = null): static;
    public function replaceKeys(ScalarCollectionInterface $collection): static;
    public function changeKeys(callable $callback): static;
    public function setKeyCase(bool $toUpper = true): static;
    /**
     * @param TValue $value
     * @param TKey $key
     */
    public function set(mixed $value, mixed $key = null): static;
    /** @param TKey $key */
    public function unset(mixed $key): static;
    /** @param TValue $value */
    public function fill(int $length, mixed $value): static;
    public function push(self $collection): static;
    public function unshift(self $collection): static;
    public function shuffle(): static;
    public function sort(bool $asc = true, ?bool $withKeys = null, ?callable $callable = null): static;
    public function unique(): static;
    public function setImmutable(bool $immutable = true): static;

    /** @param TValue $value */
    public static function isValidValue(mixed $value): bool;
    /** @param TKey$key */
    public static function isValidKey(mixed $key): bool;
    public function isEmpty(): bool;
    public function isImmutable(): bool;
    public function isList(): bool;
    /** @param TKey $key */
    public function keyExists(mixed $key): bool;
    public function equals(self $collection, bool $strict = false): bool;
    /** @param TValue $value */
    public function has(mixed $value, bool $strict = false): bool;

    /** @return TValue|null */
    public function first(): mixed;
    /** @return TValue|null */
    public function last(): mixed;
    public function reduce(callable $callback, $initial = null): mixed;
    /** @return TValue|null */
    public function shift(): mixed;
    /** @return TValue|null */
    public function pop(): mixed;

    /** @return TValue|null */
    public function keyByPosition(int $position): mixed;
    /** @return TValue|null */
    public function firstKey(): mixed;
    /** @return TValue|null */
    public function lastKey(): mixed;
    /** @return TValue|null */
    public function randomKey(): mixed;
    /**
     * @param TValue $value
     * @return TValue|null
     */
    public function search(mixed $value, bool $strict = false): mixed;
    /** @return TKey */
    public function find(callable $needle): mixed;
    /** @return array<TValue, TKey> */
    public function toArray(): array;
    public function count(): int;

    /** @return Traversable<TKey, TValue> */
    public function getIterator(): Traversable;
    /** @param TKey $offset */
    public function offsetExists(mixed $offset): bool;
    /**
     * @param TKey $offset
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed;
    /**
     * @param TKey $offset
     * @param TValue $value
     */
    public function offsetSet(mixed $offset, mixed $value): void;
    /** @param TKey $offset */
    public function offsetUnset(mixed $offset): void;
}