<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use ArrayAccess;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template T
 * @extends ArrayAccess<array-key, T>
 * @extends IteratorAggregate<array-key, T>
 */
interface CollectionInterface extends IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @param array<array-key, T> $items */
    public static function fromArray(array $items, bool $immutable = true): static;
    public static function fromInstance(self $collection, bool $immutable = true): static;
    public static function fromFillKeys(ScalarCollectionInterface $collection, $value, bool $immutable = true): static;
    /** @param T $value */
    public static function fromFill(int $startIndex, int $count, mixed $value, bool $immutable = true): static;
    public static function fromColumn(
        ObjectCollectionInterface $collection,
        string $columnKey,
        string $indexKey = null,
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

    public function reverse(bool $preserveKeys = false): static;
    public function filter(callable $callback = null): static;
    public function slice(): static;
    public function splice(int $offset = 0, int $length = null, self $replacement = null): static;
    public function replaceKeys(ScalarCollectionInterface $collection): static;
    public function changeKeys(callable $callback): static;
    public function setKeyCase(bool $toUpper = true): static;
    /** @param T $value */
    public function set(mixed $value, int|string|null $key = null): static;
    public function unset(int|string $key): static;
    /** @param T $value */
    public function fill(int $length, mixed $value): static;
    /** @param array<array-key, T> $values */
    public function push(...$values): static;
    /** @param array<array-key, T> $values */
    public function unshift(...$values): static;
    public function toList(): static;
    public function shuffle(): static;
    public function sort(bool $asc = true, ?bool $withKeys = null, ?callable $callable = null): static;
    public function unique(): static;
    public function setImmutable(bool $immutable = true): static;

    /** @param T $item */
    public static function isValidItem(mixed $item): bool;
    public function isEmpty(): bool;
    public function isImmutable(): bool;
    public function isList(): bool;
    public function keyExists(string|int $key): bool;
    public function equals(self $collection): bool;
    /** @param T $needle */
    public function has(mixed $needle, bool $strict = false): bool;

    /** @return T */
    public function first(): mixed;
    /** @return T */
    public function last(): mixed;
    /** @return T */
    public function reduce(callable $callback, $initial = null): mixed;
    /** @return T */
    public function shift(): mixed;
    /** @return T */
    public function pop(): mixed;

    public function keyByPosition(int $position): int|string|null;
    public function firstKey(): int|string|null;
    public function lastKey(): int|string|null;
    public function randomKey(): int|string|null;
    /** @param T $needle */
    public function search(mixed $needle, bool $strict = false): int|string|null;
    /** @param T $needle */
    public function find(callable $needle): int|string|null;

    /** @return array<T> */
    public function toArray(): array;
    public function count(): int;
}