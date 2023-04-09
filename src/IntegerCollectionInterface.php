<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template TValue
 * @template TKey
 * @extends CollectionInterface<TKey, TValue>
 * @extends NumberCollectionInterface<TKey, TValue>
 */
interface IntegerCollectionInterface extends NumberCollectionInterface
{
    public static function fromKeys(CollectionInterface $collection, bool $immutable = true): static;
    public static function fromKeysWithFilter(
        CollectionInterface $collection,
        mixed $filterValue,
        bool $strict = false,
        bool $immutable = true,
    ): static;
    public static function fromRandomKeys(CollectionInterface $collection, int $count, bool $immutable = true): static;
    public static function range(int $start, int $end, int $step, bool $immutable = true): static;
}
