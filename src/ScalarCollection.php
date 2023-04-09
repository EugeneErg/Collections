<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/** @implements CollectionInterface<scalar> */
class ScalarCollection extends MixedCollection implements ScalarCollectionInterface
{
    protected const VALUE_TYPE = 'is_scalar';

    public function fromFlip(ScalarCollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray(array_flip($collection->toArray()), $immutable);
    }
}
