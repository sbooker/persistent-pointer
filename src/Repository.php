<?php

declare(strict_types=1);

namespace Sbooker\PersistentPointer;

final class Repository
{
    private PointerStorage $storage;

    public function __construct(PointerStorage $storage)
    {
        $this->storage = $storage;
    }

    public function getLocked(string $name): Pointer
    {
        $pointer = $this->storage->getAndLock($name);
        if (null === $pointer) {
            $pointer = new Pointer($name);
            $this->storage->add($pointer);
        }

        return $pointer;
    }
}