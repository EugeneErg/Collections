<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template T
 * @extends CollectionInterface<float|int>
 * @implements NumberCollectionInterface<float|int>
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, int|float $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, bool $value, int|float $immutable = true)
 * @method static set(int|float $value, int|string|null $key = null)
 * @method static fromFillPad(int $length, int|float $value)
 * @method static push(int|float ...$values)
 * @method static unshift(int|float ...$values)
 * @method static bool isValidItem(int|float $item)
 * @method bool has(int|float $needle, bool $strict = false)
 * @method int|float offsetGet(int|string|null $offset)
 * @method int|string|null search(int|float $needle, bool $strict = false)
 * @method int[]|float[] getIterator()
 * @method int[]|float[] toArray()
 * @method void offsetSet(int|string|null $offset, int|float $value)
 */
interface NumberCollectionInterface extends CollectionInterface
{
    public static function fromCountValues(ScalarCollectionInterface $collection, bool $immutable = true): static;
    /** @return T */
    public function product(): mixed;
    /** @return T */
    public function sum(): mixed;
    public function sort(
        bool $asc = true,
        ?bool $withKeys = null,
        ?callable $callable = null,
        bool $asString = false,
    ): static;
}
