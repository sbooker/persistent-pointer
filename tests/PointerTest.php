<?php

declare(strict_types=1);

namespace Tests\Sbooker\PersistentPointer;

use PHPUnit\Framework\TestCase;
use Sbooker\PersistentPointer\Pointer;

class PointerTest extends TestCase
{
    public function testCreate(): void
    {
        $pointer = new Pointer('test');

        $this->assertEquals(0, $pointer->getValue());
    }

    /**
     * @dataProvider increaseDataProvider
     */
    public function testIncrease(int $firstIncreaseValue, int $secondIncreaceValue, int $expectedValue): void
    {
        $pointer = new Pointer('test');

        $pointer->increaseTo($firstIncreaseValue);
        $pointer->increaseTo($secondIncreaceValue);

        $this->assertEquals($expectedValue, $pointer->getValue());
    }

    public function increaseDataProvider(): array
    {
        return [
            [ 1 , 2, 2, ],
            [ 2 , 3, 3, ],
            [ 1 , 5, 5, ],
        ];
    }

    /**
     * @dataProvider decreaseDataProvider
     */
    public function testDecrease(int $increaseValue, int $decreaseValue): void
    {
        $pointer = new Pointer('test');

        $pointer->increaseTo($increaseValue);
        $this->expectException(\InvalidArgumentException::class);
        $pointer->increaseTo($decreaseValue);
    }

    public function decreaseDataProvider(): array
    {
        return [
            [ 1, 0 ],
            [ 0, -1 ],
            [ 5, 1 ],
        ];
    }
}