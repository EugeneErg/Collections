<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template T
 * @extends CollectionInterface<T>
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, object $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, object $value, bool $immutable = true)
 * @method static set(object $value, int|string|null $key = null)
 * @method static fill(int $length, object $value)
 * @method static push(object ...$values)
 * @method static unshift(object ...$values)
 * @method bool isValidItem(object $item)
 * @method bool has(object $needle, bool $strict = false)
 * @method object offsetGet(int|string|null $offset)
 * @method int|string|null search(object $needle, bool $strict = false)
 * @method object[] getIterator()
 * @method object[] toArray()
 * @method void offsetSet(int|string|null $offset, object $value)
 */
interface ObjectCollectionInterface extends CollectionInterface
{
    public function changeKeysFromValue(string $propertyName): static;
}
