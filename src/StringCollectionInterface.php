<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\Enums\SortTypeEnum;

/**
 * @template TValue
 * @template TKey
 * @extends CollectionInterface<TKey, TValue>
 */
interface StringCollectionInterface extends CollectionInterface
{
    public static function fromKeys(CollectionInterface $collection, bool $immutable = true): static;
    public static function fromKeysWithFilter(
        CollectionInterface $collection,
        mixed $filterValue,
        bool $strict = false,
        bool $immutable = true,
    ): static;
    public static function fromRandomKeys(CollectionInterface $collection, int $count, bool $immutable = true): static;
    public static function range(string $start, string $end, int $step, bool $immutable = true): static;
    public function sort(
        bool $asc = true,
        bool $withKeys = null,
        callable|SortTypeEnum|null $callable = null,
        bool $ignoreCase = false,
    ): static;
    public function unique(bool $asNumeric = false): static;
}
