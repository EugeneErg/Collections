<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @extends ObjectCollectionInterface<CollectionInterface>
 * @method static static fromFillKeys(ScalarCollectionInterface $collection, CollectionInterface $value, bool $immutable = true)
 * @method static static fromFill(int $startIndex, int $count, CollectionInterface $value, bool $immutable = true)
 * @method static set(CollectionInterface $value, int|string|null $key = null)
 * @method static fill(int $length, CollectionInterface $value)
 * @method static push(CollectionInterface ...$values)
 * @method static unshift(CollectionInterface ...$values)
 * @method bool isValidItem(CollectionInterface $item)
 * @method bool has(CollectionInterface $needle, bool $strict = false)
 * @method CollectionInterface offsetGet(int|string|null $offset)
 * @method int|string|null search(CollectionInterface $needle, bool $strict = false)
 * @method CollectionInterface[] getIterator()
 * @method CollectionInterface[] toArray()
 * @method void offsetSet(int|string|null $offset, CollectionInterface $value)
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
