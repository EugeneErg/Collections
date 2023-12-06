<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use IntBackedEnum;

class IntBackedEnumCollection extends ObjectCollection implements ObjectCollectionInterface
{
    protected const VALUE_TYPE = IntBackedEnum::class;
}
