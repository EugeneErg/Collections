<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use StringBackedEnum;

class StringBackedEnumCollection extends ObjectCollection implements ObjectCollectionInterface
{
    protected const VALUE_TYPE = StringBackedEnum::class;
}
