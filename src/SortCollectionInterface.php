<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\DataTransferObjects\Sort;

/**
 * @extends IntegerCollectionInterface<Sort>
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, Sort $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, Sort $value, bool $immutable = true)
 * @method static set(Sort $value, int|string|null $key = null)
 * @method static fill(int $length, Sort $value)
 * @method static push(Sort ...$values)
 * @method static unshift(Sort ...$values)
 * @method static bool isValidItem(Sort $item)
 * @method bool has(Sort $needle, bool $strict = false)
 * @method Sort offsetGet(int|string|null $offset)
 * @method int|string|null search(Sort $needle, bool $strict = false)
 * @method Sort[] getIterator()
 * @method Sort[] toArray()
 * @method void offsetSet(int|string|null $offset, Sort $value)
 */
interface SortCollectionInterface extends ObjectCollectionInterface
{
}
