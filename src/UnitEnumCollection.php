<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use UnitEnum;

class UnitEnumCollection extends ObjectCollection implements ObjectCollectionInterface
{
    protected const VALUE_TYPE = UnitEnum::class;
}
