<?php

declare(strict_types=1);

namespace Sbooker\PersistentPointer;

final class Pointer
{
    private string $name;

    private int $value;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->value = 0;
    }

    public function getValue(): int
    {
        return (int)$this->value;
    }

    public function increaseTo(int $value): void
    {
        if ($value < $this->value) {
            throw new \InvalidArgumentException();
        }

        $this->value = $value;
    }
}