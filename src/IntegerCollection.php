<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @implements CollectionInterface<int>
 * @implements FloatCollectionInterface<int>
 */
class IntegerCollection extends NumberCollection implements IntegerCollectionInterface
{
    protected const VALUE_TYPE = 'is_integer';

    public static function fromKeys(CollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray(array_keys($collection->toArray()), $immutable);
    }

    public static function fromKeysWithFilter(
        CollectionInterface $collection,
        mixed $filterValue,
        bool $strict = false,
        bool $immutable = true,
    ): static {
        return static::fromArray(array_keys($collection->toArray(), $filterValue, $strict), $immutable);
    }

    public static function fromRandomKeys(CollectionInterface $collection, int $count, bool $immutable = true): static
    {
        return static::fromArray(
            $count === 1 ? [array_rand($collection->toArray())] : array_rand($collection->toArray(), $count),
            $immutable,
        );
    }

    public static function range(int $start, int $end, int $step, bool $immutable = true): static
    {
        return static::fromArray(range($start, $end, $step), $immutable);
    }
}
