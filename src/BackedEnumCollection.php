<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use BackedEnum;

class BackedEnumCollection extends ObjectCollection implements ObjectCollectionInterface
{
    protected const VALUE_TYPE = BackedEnum::class;
}
