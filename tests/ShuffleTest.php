<?php

namespace Tests;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Valorin\Random\Random;

class ShuffleTest extends TestCase
{
    use Assertions;

    public function testShuffleString()
    {
        $string = 'original';
        $shuffled = Random::shuffle($string);

        $this->assertIsString($shuffled);
        $this->assertEquals(strlen($string), strlen($shuffled));

        $this->assertVaries($string, function () use ($string) {
            return Random::shuffle($string);
        }, 'Shuffling should not always reproduce the original order.');
    }

    public function testShuffleArrayWithoutPreservingKeys()
    {
        $array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];
        $shuffled = Random::shuffle($array, $preserveKeys = false);

        $this->assertIsArray($shuffled);
        $this->assertEquals(count($array), count($shuffled));
        $this->assertSame(range(0, 8), array_keys($shuffled), 'Keys were not re-indexed.');

        $this->assertVaries($array, function () use ($array) {
            return Random::shuffle($array, false);
        }, 'Shuffling should not always reproduce the original order.');
    }

    public function testShuffleArrayPreservingKeys()
    {
        $array = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];
        $shuffled = Random::shuffle($array, $preserveKeys = true);

        for ($j = 0; $j < 9; $j++) {
            $this->assertSame($array[$j], $shuffled[$j], "Key {$j} was not preserved.");
        }

        $this->assertVaries(range(0, 8), function () use ($array) {
            return array_keys(Random::shuffle($array, true));
        }, 'Shuffling should vary the order while preserving keys.');
    }

    public function testShuffleCollectionWithoutPreservingKey()
    {
        $collection = new Collection(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i']);
        $shuffled = Random::shuffle($collection, $preserveKeys = false);

        $this->assertInstanceOf(Collection::class, $shuffled);
        $this->assertSame(range(0, 8), $shuffled->keys()->toArray(), 'Keys were not re-indexed.');

        $this->assertVaries($collection->toArray(), function () use ($collection) {
            return Random::shuffle($collection, false)->toArray();
        }, 'Shuffling should not always reproduce the original order.');
    }

    public function testShuffleCollectionPreservingKey()
    {
        $collection = new Collection(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i']);
        $shuffled = Random::shuffle($collection, $preserveKeys = true);

        $this->assertInstanceOf(Collection::class, $shuffled);

        for ($j = 0; $j < 9; $j++) {
            $this->assertSame($collection[$j], $shuffled[$j], "Key {$j} was not preserved.");
        }

        $this->assertVaries(range(0, 8), function () use ($collection) {
            return Random::shuffle($collection, true)->keys()->toArray();
        }, 'Shuffling should vary the order while preserving keys.');
    }
}
