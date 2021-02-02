<?php
declare(strict_types=1);

namespace Sbooker\PersistentPointer;

interface PointerStorage
{
    public function add(Pointer $pointer): void;

    public function getAndLock(string $name): ?Pointer;
}