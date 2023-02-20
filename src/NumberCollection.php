<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template T
 * @extends ScalarCollection<T>
 * @implements NumberCollectionInterface<T>
 */
class NumberCollection extends ScalarCollection implements NumberCollectionInterface
{
    public static function fromCountValues(ScalarCollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray(array_count_values($collection->toArray()), $immutable);
    }

    /** @inheritDoc */
    public function product(): float
    {
        return array_product($this->toArray());
    }

    /** @inheritDoc */
    public function sum(): float
    {
        return array_sum($this->toArray());
    }

    public function sort(
        bool $asc = true,
        ?bool $withKeys = null,
        ?callable $callable = null,
        bool $asString = false,
    ): static {
        if ($callable !== null && $asString) {
            $callable = fn (float|int $value1, float|int $value2): int
                => (int) $callable((string) $value1, (string) $value2);
        }

        if ($asString === false || $callable !== null) {
            return parent::sort($asc, $withKeys, $callable);
        }

        $result = $this->getMutable();

        if ($withKeys === null) {
            $asc ? asort($result->items, SORT_STRING) : arsort($result->items, SORT_STRING);
        } elseif ($withKeys === true) {
            $asc ? ksort($result->items, SORT_STRING) : krsort($result->items, SORT_STRING);
        } else {
            $asc ? sort($result->items, SORT_STRING) : rsort($result->items, SORT_STRING);
        }

        return $result;
    }
}
