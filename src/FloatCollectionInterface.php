<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template T
 * @extends NumberCollection<T>
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, float $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, float $value, bool $immutable = true)
 * @method static set(float $value, int|string|null $key = null)
 * @method static fill(int $length, float $value)
 * @method static push(float ...$values)
 * @method static unshift(float ...$values)
 * @method static bool isValidItem(float $item)
 * @method bool has(float $needle, bool $strict = false)
 * @method float offsetGet(int|string|null $offset)
 * @method int|string|null search(float $needle, bool $strict = false)
 * @method float[] getIterator()
 * @method float[] toArray()
 * @method void offsetSet(int|string|null $offset, float $value)
 */
interface FloatCollectionInterface extends NumberCollectionInterface
{
    public static function range(float $start, float $end, float $step, bool $immutable = true): static;
}
