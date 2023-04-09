<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/** @implements CollectionInterface<bool> */
class BooleanCollection extends MixedCollection
{
    protected const VALUE_TYPE = 'is_bool';
}
