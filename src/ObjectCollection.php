<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @extends MixedCollection<object>
 * @implements ObjectCollectionInterface<object>
 */
class ObjectCollection extends MixedCollection implements ObjectCollectionInterface
{
    protected const VALUE_TYPE = 'is_object';

    public function changeKeysFromValue(string $propertyName): static
    {
        return $this->setItemsWithoutValidate(array_column($this->toArray(), null, $propertyName));
    }
}
