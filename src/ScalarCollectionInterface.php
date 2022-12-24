<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template T
 * @extends CollectionInterface<T>
 */
interface ScalarCollectionInterface extends CollectionInterface
{
    public function fromFlip(ScalarCollectionInterface $collection, bool $immutable = true): static;
}
