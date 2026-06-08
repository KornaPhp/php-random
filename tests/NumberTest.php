<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Valorin\Random\Random;

class NumberTest extends TestCase
{
    use Assertions;

    public function testNumbersWithinLimits()
    {
        for ($i = 0; $i < 10; $i++) {
            $number = Random::number(1, 100);

            $this->assertIsInt($number);
            $this->assertGreaterThanOrEqual(1, $number);
            $this->assertLessThanOrEqual(100, $number);
        }
    }

    public function testDifferentNumbers()
    {
        $this->assertVaries(Random::number(1, 100000), function () {
            return Random::number(1, 100000);
        }, 'Random::number() returned the same value on every attempt.');
    }
}
