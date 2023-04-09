<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\DataTransferObjects\Sort;

/**
 * @implements ObjectCollectionInterface<Sort>
 * @implements SortCollectionInterface<Sort>
 * @implements CollectionInterface<Sort>
 */
class SortCollection extends ObjectCollection implements SortCollectionInterface
{
    protected const VALUE_TYPE = Sort::class;
}
