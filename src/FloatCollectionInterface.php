<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template TValue
 * @template TKey
 * @extends CollectionInterface<TKey, TValue>
 * @extends NumberCollectionInterface<TKey, TValue>
 */
interface FloatCollectionInterface extends NumberCollectionInterface
{
    public static function range(float $start, float $end, float $step, bool $immutable = true): static;
}
