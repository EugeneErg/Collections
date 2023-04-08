<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

use EugeneErg\Collections\DataTransferObjects\Sort;

/**
 * @extends ObjectCollection<CollectionInterface>
 * @implements CollectionCollectionInterface<CollectionInterface>
 */
class CollectionCollection extends ObjectCollection implements CollectionCollectionInterface
{
    protected const VALUE_TYPE = CollectionInterface::class;

    public function chunk(string|int $key, int $length, bool $preserveKeys = false): ?static
    {
        /** @var CollectionInterface $collection */
        $collection = $this[$key] ?? null;

        if ($collection === null) {
            return null;
        }

        return $this->setItemsWithoutValidate(array_map(
            fn (array $data) => static::itemFromArray($data, $collection->isImmutable()),
            array_chunk($collection->toArray(), $length, $preserveKeys),
        ));
    }

    public function merge(bool $recursive = false, bool $immutable = true): CollectionInterface
    {
        /** @var CollectionInterface $itemType */
        $itemType = static::VALUE_TYPE;

        return $recursive && is_subclass_of($itemType, CollectionCollectionInterface::class)
            ? $itemType::fromArrayRecursive(array_merge_recursive( ...$this->toArrayRecursive()), null, $immutable)
            : $itemType::fromArray(array_merge(...$this->toArray()), $immutable);
    }

    public function replace(bool $recursive = false, bool $immutable = true): CollectionInterface
    {
        /** @var CollectionInterface $itemType */
        $itemType = static::VALUE_TYPE;

        return $recursive && is_subclass_of($itemType, CollectionCollectionInterface::class)
            ? $itemType::fromArrayRecursive(array_replace_recursive( ...$this->toArrayRecursive()), null, $immutable)
            : $itemType::fromArray(array_replace(...$this->toArray()), $immutable);
    }

    public static function itemFromArray(array $data, bool $immutable = true): CollectionInterface
    {
        /** @var CollectionInterface $itemType */
        $itemType = static::VALUE_TYPE;

        return $itemType::fromArray($data, $immutable);
    }

    public static function fromArrayRecursive(array $items, ?int $level = null, bool $immutable = true): static
    {
        $result = [];
        /** @var CollectionCollectionInterface $class */
        $class = static::VALUE_TYPE;

        foreach ($items as $key => $value) {
            $result[$key] = is_subclass_of($class, CollectionCollectionInterface::class) && ($level > 0 || $level === null)
                ? $class::fromArrayRecursive($value)
                : $class::fromArray($value);
        }

        return static::fromArray($result, $immutable);
    }

    public function toArrayRecursive(?int $level = null): array
    {
        $result = [];

        /** @var CollectionInterface $value */
        foreach ($this as $key => $value) {
            $result[$key] = $value instanceof CollectionCollectionInterface && ($level > 0 || $level === null)
                ? $value->toArrayRecursive($level - 1)
                : $value->toArray();
        }

        return $result;
    }

    public function multiSort(SortCollectionInterface $collection): static
    {
        $items = $this->toArrayRecursive(1);
        $sorted = [];

        foreach ($items as $key => &$item) {
            $sorted[] = &$item;

            if (isset($collection[$key])) {
                /** @var Sort $sort */
                $sort = $collection[$key];
                $sorted[] = $sort->type->value ?? SORT_REGULAR;

                if (!$sort->asc) {
                    $sorted[] = SORT_DESC;
                }
            }
        }

        unset($item);
        $result = $this->getMutable();
        $result->items = [];
        array_multisort($sorted);
        /** @var CollectionInterface $type */
        $type = static::VALUE_TYPE;

        foreach ($items as $key => $item) {
            $result->items[$key] = $type::fromArray($item);
        }

        return $result;
    }
}