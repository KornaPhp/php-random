<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Valorin\Random\Random;

class StringTest extends TestCase
{
    use Assertions;

    public function testLength()
    {
        for ($length = 10; $length < 20; $length++) {
            $string = Random::string($length);

            $this->assertIsString($string);
            $this->assertEquals($length, strlen($string));
        }
    }

    public function testHasLowerCase()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string();

            $valid = $valid || preg_match('/[a-z]/', $string);
        }

        $this->assertTrue($valid, 'Lowercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testNoLowerCase()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string(
                $length = 32,
                $lower = false,
                $upper = true,
                $numbers = true,
                $symbols = true,
                $requireAll = false
            );

            $valid = $valid || preg_match('/[^a-z]/', $string);
        }

        $this->assertTrue($valid, 'Lowercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testHasNumbers()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string();

            $valid = $valid || preg_match('/[0-9]/', $string);
        }

        $this->assertTrue($valid, 'Uppercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testNoNumbers()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string(
                $length = 32,
                $lower = true,
                $upper = true,
                $numbers = false,
                $symbols = true,
                $requireAll = false
            );

            $valid = $valid || preg_match('/[^0-9]/', $string);
        }

        $this->assertTrue($valid, 'Uppercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testHasUpperCase()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string();

            $valid = $valid || preg_match('/[A-Z]/', $string);
        }

        $this->assertTrue($valid, 'Uppercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testNoUpperCase()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string(
                $length = 32,
                $lower = true,
                $upper = false,
                $numbers = true,
                $symbols = true,
                $requireAll = false
            );

            $valid = $valid || preg_match('/[^A-Z]/', $string);
        }

        $this->assertTrue($valid, 'Uppercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testHasSymbols()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string();

            $valid = $valid || preg_match('/[^a-zA-Z0-9]/', $string);
        }

        $this->assertTrue($valid, 'Uppercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testNoSymbols()
    {
        $valid = false;
        for ($i = 0; $i < 10; $i++) {
            $string = Random::string(
                $length = 32,
                $lower = true,
                $upper = true,
                $numbers = true,
                $symbols = false,
                $requireAll = false
            );

            $valid = $valid || preg_match('/^[a-zA-Z0-9]+$/', $string);
        }

        $this->assertTrue($valid, 'Uppercase letters were never seen in the result. (Low chance of a false positive.)');
    }

    public function testRequireAll()
    {
        for ($i = 0; $i < 100; $i++) {
            $string = Random::string(
                $length = 32,
                $lower = true,
                $upper = true,
                $numbers = true,
                $symbols = true,
                $requireAll = true
            );

            $this->assertRegExpCustom('/[a-z]/', $string);
            $this->assertRegExpCustom('/[A-Z]/', $string);
            $this->assertRegExpCustom('/[0-9]/', $string);
            $this->assertRegExpCustom('/[^a-zA-Z0-9]/', $string);
        }
    }

    public function testCantRequireAllWithoutEnoughLength()
    {
        for ($i = 0; $i < 100; $i++) {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('Length not enough to requireAll!');

            Random::string(
                $length = 2,
                $lower = true,
                $upper = true,
                $numbers = false,
                $symbols = true,
                $requireAll = true
            );
        }
    }

    public function testLetters()
    {
        for ($i = 0; $i < 100; $i++) {
            $string = Random::letters();

            $this->assertIsString($string);
            $this->assertRegExpCustom('/^[a-zA-Z]+$/', $string);
        }
    }

    public function testToken()
    {
        for ($i = 0; $i < 100; $i++) {
            $string = Random::token();

            $this->assertIsString($string);
            $this->assertRegExpCustom('/^[a-zA-Z0-9]+$/', $string);
        }
    }

    public function testPassword()
    {
        $valid = false;

        for ($i = 0; $i < 10; $i++) {
            $string = Random::password();

            $valid = $valid || preg_match('/[a-z]/', $string);
            $valid = $valid || preg_match('/[0-9]/', $string);
            $valid = $valid || preg_match('/[A-Z]/', $string);
            $valid = $valid || preg_match('/[^a-zA-Z0-9]/', $string);
        }

        $this->assertTrue($valid, 'Not all character types were seen in the result. (Low chance of a false positive.)');
    }

    public function testDashed()
    {
        for ($i = 0; $i < 100; $i++) {
            $string = Random::dashed($length = 25, $delimiter = '-', $chunkLength = 5, $mixedCase = true);

            $this->assertIsString($string);
            $this->assertRegExpCustom('/^[a-zA-Z0-9]{5}\-[a-zA-Z0-9]{5}\-[a-zA-Z0-9]{5}\-[a-zA-Z0-9]{5}\-[a-zA-Z0-9]{5}$/', $string);
        }
    }

    public function testDashedNoLowerCase()
    {
        for ($i = 0; $i < 100; $i++) {
            $string = Random::dashed($length = 25, $delimiter = '-', $chunkLength = 5, $mixedCase = false);

            $this->assertIsString($string);
            $this->assertRegExpCustom('/^[A-Z0-9]{5}\-[A-Z0-9]{5}\-[A-Z0-9]{5}\-[A-Z0-9]{5}\-[A-Z0-9]{5}$/', $string);
        }
    }

    public function testCustomCharacterSets()
    {
        $generator = Random::useLower(range('a', 'f'))
            ->useUpper(range('G', 'L'))
            ->useNumbers(range(2, 6))
            ->useSymbols(['!', '@', '#', '$', '%', '^', '&', '*', '(', ')']);

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = true,
                $upper = true,
                $numbers = true,
                $symbols = true,
                $requireAll = false
            );

            $this->assertRegExpCustom('/^[a-fG-L2-6!@#$%^&*()]+$/', $string);
        }
    }

    public function testCustomCharacterSetsAllRequired()
    {
        $generator = Random::useLower(range('a', 'f'))
            ->useUpper(range('G', 'L'))
            ->useNumbers(range(2, 6))
            ->useSymbols(['!', '@', '#', '$', '%', '^', '&', '*', '(', ')']);

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = true,
                $upper = true,
                $numbers = true,
                $symbols = true,
                $requireAll = true
            );

            $this->assertRegExpCustom('/[a-f]/', $string);
            $this->assertRegExpCustom('/[G-L]/', $string);
            $this->assertRegExpCustom('/[2-6]/', $string);
            $this->assertRegExpCustom('/[!@#$%^&*()]/', $string);
            $this->assertRegExpCustom('/^[a-fG-L2-6!@#$%^&*()]+$/', $string);
        }
    }

    public function testUnexpectedLowerInput()
    {
        $generator = Random::useLower(range('A', 'f'));

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = true,
                $upper = false,
                $numbers = false,
                $symbols = false,
                $requireAll = false
            );

            $this->assertRegExpCustom('/[a-f]/', $string);
            $this->assertRegExpCustom('/[^A-Z]/', $string);
        }
    }

    public function testUnexpectedUpperInput()
    {
        $generator = Random::useUpper(range('A', 'f'));

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = false,
                $upper = true,
                $numbers = false,
                $symbols = false,
                $requireAll = false
            );

            $this->assertRegExpCustom('/[A-Z]/', $string);
            $this->assertRegExpCustom('/[^a-f]/', $string);
        }
    }

    public function testUnexpectedNumbersInput()
    {
        $generator = Random::useNumbers([2, 3, 4, 'F', 'a', '#', 88, '9']);

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = false,
                $upper = false,
                $numbers = true,
                $symbols = false,
                $requireAll = false
            );

            $this->assertRegExpCustom('/[2|3|4|9]/', $string);
        }
    }

    public function testUnexpectedSymbolsInput()
    {
        $generator = Random::useSymbols([2, 3, 4, 'F', 'a', '#', '!', '9']);

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = false,
                $upper = false,
                $numbers = false,
                $symbols = true,
                $requireAll = false
            );

            $this->assertRegExpCustom('/^[2-4Fa#!9]+$/', $string);
        }
    }

    public function testLowerInputFromString()
    {
        $generator = Random::useLower('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = true,
                $upper = false,
                $numbers = false,
                $symbols = false,
                $requireAll = false
            );

            $this->assertRegExpCustom('/^[a-z]+$/', $string);
        }
    }

    public function testUpperInputFromString()
    {
        $generator = Random::useUpper('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = false,
                $upper = true,
                $numbers = false,
                $symbols = false,
                $requireAll = false
            );

            $this->assertRegExpCustom('/^[A-Z]+$/', $string);
        }
    }

    public function testNumbersInputFromString()
    {
        $generator = Random::useNumbers("02468");

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = false,
                $upper = false,
                $numbers = true,
                $symbols = false,
                $requireAll = false
            );

            $this->assertRegExpCustom('/[0|2|4|6|8]/', $string);
        }
    }

    public function testSymbolsInputFromString()
    {
        $generator = Random::useSymbols('abcdefghijklmnopqrstuvwxyz12345^&*()');

        for ($i = 0; $i < 100; $i++) {
            $string = $generator->string(
                $length = 32,
                $lower = false,
                $upper = false,
                $numbers = false,
                $symbols = true,
                $requireAll = false
            );

            $this->assertRegExpCustom('/^[a-z12345^&*()]+$/', $string);
        }
    }
}
