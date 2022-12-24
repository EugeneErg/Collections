<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template T
 * @extends MixedCollection<T>
 * @implements SortCollectionInterface<T>
 */
class ScalarCollection extends MixedCollection implements ScalarCollectionInterface
{
    protected const ITEM_TYPE = 'is_scalar';

    public function fromFlip(ScalarCollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray(array_flip($collection->toArray()), $immutable);
    }
}
