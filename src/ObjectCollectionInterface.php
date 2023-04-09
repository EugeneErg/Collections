<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template TValue
 * @template TKey
 * @extends CollectionInterface<TKey, TValue>
 */
interface ObjectCollectionInterface extends CollectionInterface
{
    public function changeKeysFromValue(string $propertyName): static;
}
