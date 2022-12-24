<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\DataTransferObjects\Sort;

/**
 * @extends ObjectCollection<Sort>
 * @implements SortCollectionInterface<Sort>
 */
class SortCollection extends ObjectCollection implements SortCollectionInterface
{
    protected const ITEM_TYPE = Sort::class;
}
