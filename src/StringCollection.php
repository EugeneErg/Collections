<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\Enums\SortTypeEnum;

class StringCollection extends ScalarCollection implements StringCollectionInterface
{
    protected const ITEM_TYPE = 'is_string';

    public static function fromKeys(CollectionInterface $collection, bool $immutable = true): static
    {
        return static::fromArray(array_keys($collection->toArray()), $immutable);
    }

    public static function fromKeysWithFilter(
        CollectionInterface $collection,
        mixed $filterValue,
        bool $strict = false,
        bool $immutable = true,
    ): static {
        return static::fromArray(array_keys($collection->toArray(), $filterValue, $strict), $immutable);
    }

    public static function fromRandomKeys(CollectionInterface $collection, int $count, bool $immutable = true): static
    {
        return static::fromArray(
            $count === 1 ? [array_rand($collection->toArray())] : array_rand($collection->toArray(), $count),
            $immutable,
        );
    }

    public static function range(string $start, string $end, int $step, bool $immutable = true): static
    {
        return static::fromArray(
            array_map(fn (int|float|string $value): string => (string) $value, range($start, $end, $step)),
            $immutable,
        );
    }

    public function sort(
        bool $asc = true,
        ?bool $withKeys = null,
        callable|SortTypeEnum|null $callable = null,
        bool $ignoreCase = false,
    ): static {
        if (is_callable($callable) && $ignoreCase) {
            $callable = fn (string $value1, string $value2): int
                => (int) $callable(strtolower($value1), strtolower($value2));
        }

        if (in_array($callable, [null, SortTypeEnum::String], true) || is_callable($callable)) {
            return parent::sort($asc, $withKeys, $callable);
        }

        $result = $this->getMutableCollection();
        $flag = $callable->value | ($ignoreCase ? SORT_FLAG_CASE : 0);

        if ($withKeys === null) {
            $asc ? asort($result->items, $flag) : arsort($result->items, $flag);
        } elseif ($withKeys === true) {
            $asc ? ksort($result->items, $flag) : krsort($result->items, $flag);
        } else {
            $asc ? sort($result->items, $flag) : rsort($result->items, $flag);
        }

        return $result;
    }

    public function unique(bool $asNumeric = false): static
    {
        return $this->setItemsWithoutValidate(array_unique($this->toArray(), $asNumeric ? SORT_NUMERIC : SORT_REGULAR));
    }
}
