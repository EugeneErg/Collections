<?php

declare(strict_types=1);

namespace EugeneErg\Collections;

/**
 * @template TValue
 * @template TKey
 * @extends CollectionInterface<TKey, TValue>
 */
interface NumberCollectionInterface extends CollectionInterface
{
    public static function fromCountValues(ScalarCollectionInterface $collection, bool $immutable = true): static;
    /** @return TValue */
    public function product(): mixed;
    /** @return TValue */
    public function sum(): mixed;
    public function sort(
        bool $asc = true,
        ?bool $withKeys = null,
        ?callable $callable = null,
        bool $asString = false,
    ): static;
}
