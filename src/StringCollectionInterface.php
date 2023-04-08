<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\Enums\SortTypeEnum;

/**
 * @extends CollectionInterface<string>
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, string $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, bool $value, string $immutable = true)
 * @method static set(string $value, int|string|null $key = null)
 * @method static fromFillPad(int $length, string $value)
 * @method static push(string ...$values)
 * @method static unshift(string ...$values)
 * @method static bool isValidItem(string $item)
 * @method bool has(string $needle, bool $strict = false)
 * @method string offsetGet(int|string|null $offset)
 * @method int|string|null search(string $needle, bool $strict = false)
 * @method string[] getIterator()
 * @method string[] toArray()
 * @method void offsetSet(int|string|null $offset, string $value)
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
