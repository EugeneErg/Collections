<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @extends NumberCollection<int>
 * @implements IntegerCollectionInterface<int>
 */
class IntegerCollection extends NumberCollection implements IntegerCollectionInterface
{
    protected const ITEM_TYPE = 'is_integer';

    public static function range(int $start, int $end, int $step, bool $immutable = true): static
    {
        return static::fromArray(range($start, $end, $step), $immutable);
    }
}
