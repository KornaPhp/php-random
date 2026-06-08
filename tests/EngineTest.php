<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Random\Engine\Mt19937;
use Valorin\Random\Random;

class EngineTest extends TestCase
{
    use Assertions;

    public function testSeededEngineIsntGlobal()
    {
        $generator = Random::use(new Mt19937(3791));

        // The global generator is unseeded, so it should not be locked to the
        // seeded values. Sampling several draws keeps this from flaking on the
        // rare occasion a single global draw happens to match the seeded value.
        $this->assertVaries(14065, function () {
            return Random::number(1, 100000);
        }, 'The global generator should not always pick the seeded value.');
        $this->assertEquals(14065, $generator->number(1, 100000));

        $this->assertVaries('847994', function () {
            return Random::otp();
        }, 'The global generator should not always pick the seeded value.');
        $this->assertEquals(847994, $generator->otp());

        $this->assertVaries('hw8kXvG060UyLKq8oKyVyXsmPC5ED9pa', function () {
            return Random::token();
        }, 'The global generator should not always pick the seeded value.');
        $this->assertEquals('hw8kXvG060UyLKq8oKyVyXsmPC5ED9pa', $generator->token());
    }

    public function testSeededEngineIsUnique()
    {
        $generatorOne = Random::use(new Mt19937(1));
        $generatorTwo = Random::use(new Mt19937(2));

        $tokenOne = $generatorOne->token();
        $tokenTwo = $generatorTwo->token();

        $this->assertNotEquals($tokenOne, $tokenTwo);
    }
}
