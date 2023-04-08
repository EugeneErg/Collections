<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use ArrayAccess;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template V
 * @template K
 * @extends ArrayAccess<K, V>
 * @extends IteratorAggregate<K, V>
 */
interface CollectionInterface extends IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @param array<K, V> $items */
    public static function fromArray(array $items = [], bool $immutable = true): static;
    public static function fromMixedArray(array $items, callable $callback, bool $immutable = true): static;
    public static function fromInstance(self $collection, bool $immutable = true): static;
    public static function fromFillKeys(self $collection, mixed $value, bool $immutable = true): static;
    /** @param V $value */
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
     * @param V $value
     * @param K $key
     */
    public function set(mixed $value, mixed $key = null): static;
    /** @param K $key */
    public function unset(mixed $key): static;
    /** @param V $value */
    public function fill(int $length, mixed $value): static;
    public function push(self $collection): static;
    public function unshift(self $collection): static;
    public function shuffle(): static;
    public function sort(bool $asc = true, ?bool $withKeys = null, ?callable $callable = null): static;
    public function unique(): static;
    public function setImmutable(bool $immutable = true): static;

    /** @param V $value */
    public static function isValidValue(mixed $value): bool;
    /** @param K $key */
    public static function isValidKey(mixed $key): bool;
    public function isEmpty(): bool;
    public function isImmutable(): bool;
    public function isList(): bool;
    public function keyExists(mixed $key): bool;
    public function equals(self $collection, bool $strict = false): bool;
    /** @param V $value */
    public function has(mixed $value, bool $strict = false): bool;

    /** @return V|null */
    public function first(): mixed;
    /** @return V|null */
    public function last(): mixed;
    /** @return V|null */
    public function reduce(callable $callback, $initial = null): mixed;
    /** @return V|null */
    public function shift(): mixed;
    /** @return V|null */
    public function pop(): mixed;

    /** @return K|null */
    public function keyByPosition(int $position): mixed;
    /** @return K|null */
    public function firstKey(): mixed;
    /** @return K|null */
    public function lastKey(): mixed;
    /** @return K|null */
    public function randomKey(): mixed;
    /**
     * @param V $value
     * @return K
     */
    public function search(mixed $value, bool $strict = false): mixed;
    /** @return K */
    public function find(callable $needle): mixed;
    /** @return array<K, V> */
    public function toArray(): array;
    public function count(): int;
}