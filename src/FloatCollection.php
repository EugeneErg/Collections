<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @implements CollectionInterface<float>
 * @implements FloatCollectionInterface<float>
 */
class FloatCollection extends NumberCollection implements FloatCollectionInterface
{
    protected const VALUE_TYPE = 'is_float';

    public static function range(float $start, float $end, float $step, bool $immutable = true): static
    {
        return static::fromArray(range($start, $end, $step), $immutable);
    }
}
