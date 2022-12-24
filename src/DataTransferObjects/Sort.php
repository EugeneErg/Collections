<?php

declare(strict_types=1);

namespace EugeneErg\Collections\DataTransferObjects;

use EugeneErg\Collections\Enums\SortTypeEnum;

class Sort
{
    public function __construct(public readonly ?SortTypeEnum $type = null, public readonly bool $asc = true)
    {
    }
}
