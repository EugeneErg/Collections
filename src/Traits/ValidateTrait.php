<?php

declare(strict_types=1);

namespace EugeneErg\Collections\Traits;

use InvalidArgumentException;

trait ValidateTrait
{
    protected const ITEM_TYPE = null;

    public static function isValidItems(array $items): bool
    {
        if (static::ITEM_TYPE === null) {
            return true;
        }

        if (is_callable(static::ITEM_TYPE)) {
            foreach ($items as $item) {
                if (!(static::ITEM_TYPE)($item)) {
                    return false;
                }
            }
        } else {
            $type = static::ITEM_TYPE;

            foreach ($items as $item) {
                if (!$item instanceof $type) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function isValidItem(mixed $item): bool
    {
        return static::isValidItems([$item]);
    }

    private static function validate(mixed $item): void
    {
        if (!self::isValidItem($item)) {
            throw new InvalidArgumentException('Invalid item');
        }
    }

    private static function validateItems(array $items): void
    {
        if (!self::isValidItems($items)) {
            throw new InvalidArgumentException('Invalid items');
        }
    }
}
