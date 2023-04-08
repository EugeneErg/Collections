<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, bool $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, bool $value, bool $immutable = true)
 * @method static set(bool $value, int|string|null $key = null)
 * @method static fromFillPad(int $length, bool $value)
 * @method static push(bool ...$values)
 * @method static unshift(bool ...$values)
 * @method static bool isValidItem(bool $item)
 * @method bool has(bool $needle, bool $strict = false)
 * @method bool offsetGet(int|string|null $offset)
 * @method int|string|null search(bool $needle, bool $strict = false)
 * @method bool[] getIterator()
 * @method bool[] toArray()
 * @method void offsetSet(int|string|null $offset, bool $value)
 */
class BooleanCollection extends MixedCollection
{
    protected const VALUE_TYPE = 'is_bool';
}
