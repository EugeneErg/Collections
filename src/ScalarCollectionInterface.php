<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template TValue
 * @template TKey
 * @extends CollectionInterface<TKey, TValue>
 */
interface ScalarCollectionInterface extends CollectionInterface
{
    public static function fromFlip(ScalarCollectionInterface $collection, bool $immutable = true): static;
}
