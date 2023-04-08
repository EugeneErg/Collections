<?php

declare(strict_types=1);

namespace EugeneErg\Collections\Traits;

use Stringable;

trait ValidateTrait
{
    private static function isValidItems(
        array $items,
        callable|string|null $valueType = null,
        callable|string|null $keyType = null,
    ): bool {
        $valueCallback = self::getValidator($valueType);
        $keyCallback = self::getValidator($keyType);

        if ($valueType === null && $keyType === null) {
            return true;
        } elseif ($valueType !== null && $keyType !== null) {
            foreach ($items as $key => $value) {
                if (!$valueCallback($value) || !$keyCallback($key)) {
                    return false;
                }
            }
        } elseif ($valueType !== null) {
            foreach ($items as $value) {
                if (!$valueCallback($value)) {
                    return false;
                }
            }
        } else {
            foreach ($items as $key => $value) {
                if (!$keyCallback($key)) {
                    return false;
                }
            }
        }

        return true;
    }

    private static function getValidator(callable|string|null $type): callable
    {
        if (is_callable($type)) {
            return $type;
        }

        if ($type === null) {
            return fn (mixed $item): bool => true;
        }

        if (class_exists($type)) {
            return fn (mixed $item): bool => $item instanceof $type;
        }

        return fn (mixed $item): bool => (is_scalar($item) || $item instanceof Stringable)
            && preg_match($type, (string) $item) > 0;
    }
}
