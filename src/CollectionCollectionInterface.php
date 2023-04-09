<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template TValue
 * @template TKey
 * @extends CollectionInterface<TKey, TValue>
 * @extends ObjectCollectionInterface<TKey, TValue>
 */
interface CollectionCollectionInterface extends ObjectCollectionInterface
{
    public function chunk(string|int $key, int $length, bool $preserveKeys = false): ?static;
    public function merge(bool $recursive = false, bool $immutable = true): CollectionInterface;
    public function replace(bool $recursive = false, bool $immutable = true): CollectionInterface;
    public static function itemFromArray(array $data, bool $immutable = true): CollectionInterface;
    public static function fromArrayRecursive(array $items, ?int $level = null, bool $immutable = true): static;
    public function toArrayRecursive(?int $level = null): array;
    public function multiSort(SortCollectionInterface $collection): static;
}
