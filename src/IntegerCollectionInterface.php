<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @extends NumberCollectionInterface<int>
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, int $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, int $value, bool $immutable = true)
 * @method static set(float $value, int|string|null $key = null)
 * @method static fill(int $length, int $value)
 * @method static push(int ...$values)
 * @method static unshift(int ...$values)
 * @method bool isValidItem(int $item)
 * @method bool has(int $needle, bool $strict = false)
 * @method int offsetGet(int|string|null $offset)
 * @method int|string|null search(int $needle, bool $strict = false)
 * @method int[] getIterator()
 * @method int[] toArray()
 * @method void offsetSet(int|string|null $offset, int $value)
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
