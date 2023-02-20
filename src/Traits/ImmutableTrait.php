<?php

declare(strict_types=1);

namespace EugeneErg\Collections\Traits;

trait ImmutableTrait
{
    private bool $immutable;

    public function setImmutable(bool $immutable = true): static
    {
        $result = $immutable === $this->immutable ? $this : $this->getMutable();
        $result->immutable = $immutable;

        return $result;
    }

    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    protected function getMutable(): static
    {
        return $this->immutable ? clone $this : $this;
    }
}
