<?php

namespace Valorin\Random;

use Illuminate\Support\Collection;
use Random\Engine;
use Random\Randomizer;

class Generator
{
    /** @var \Random\Randomizer */
    protected $randomizer;

    /** @var array */
    protected $lowerCharacters;

    /** @var array */
    protected $upperCharacters;

    /** @var array */
    protected $numberCharacters;

    /** @var array */
    protected $symbolCharacters;

    /**
     * Construct a new Random\Generator, optionally with a Randomizer Engine.
     *
     * @param \Random\Engine|null $engine
     */
    public function __construct(?Engine $engine = null)
    {
        $this->randomizer = new Randomizer($engine);

        $this->lowerCharacters = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        ];
        $this->upperCharacters = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];
        $this->numberCharacters = [
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        ];
        $this->symbolCharacters = [
            '!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/', ':',
            ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|', '}', '~',
        ];
    }

    /**
     * Generate a random number between $min and $max, inclusive.
     *
     * @param  int  $min
     * @param  int  $max
     * @return int
     */
    public function number(int $min, int $max): int
    {
        return $this->randomizer->getInt($min, $max);
    }

    /**
     * Generate a random string of $length characters, following the specified character rules.
     * If $requireAll is true, at least one character from each set will be included.
     *
     * @param  int   $length
     * @param  bool  $lower       If true, lowercase letters will be included.
     * @param  bool  $upper       If true, uppercase letters will be included.
     * @param  bool  $numbers     If true, numbers will be included.
     * @param  bool  $symbols     If true, symbols will be included.
     * @param  bool  $requireAll  If true, at least one character from each set will be included.
     * @return string
     */
    public function string(int $length = 32, bool $lower = true, bool $upper = true, bool $numbers = true, bool $symbols = true, bool $requireAll = false): string
    {
        $chars = array_filter([
            $lower ? $this->lowerCharacters : [],
            $upper ? $this->upperCharacters : [],
            $numbers ? $this->numberCharacters : [],
            $symbols ? $this->symbolCharacters : [],
        ]);

        if (! $chars) {
            throw new \InvalidArgumentException('Cannot generate random string with no character sets enabled!');
        }

        if ($requireAll && count($chars) > $length) {
            throw new \InvalidArgumentException('Length not enough to requireAll!');
        }

        $string = '';

        if ($requireAll) {
            foreach ($chars as $set) {
                $string .= $set[$this->number(0, count($set) - 1)];
            }
        }

        $chars = array_merge(...$chars);

        while (strlen($string) < $length) {
            $string .= $chars[$this->number(0, count($chars) - 1)];
        }

        if ($requireAll) {
            $string = $this->shuffle($string);
        }

        return $string;
    }

    /**
     * Generate a numeric One-Time Password (OTP) of $length digits, suitable for use in 2FA.
     * Leading zeros will be included, so the output is a string rather than an integer.
     *
     * @param  int  $length
     * @return string
     */
    public function otp(int $length = 6): string
    {
        return $this->string($length, $lower = false, $upper = false, $numbers = true, $symbols = false, $requireAll = false);
    }

    /**
     * @alias otp()
     */
    public function passcode(int $length = 6): string
    {
        return $this->otp($length);
    }

    /**
     * Generate a random string of $length lowercase and uppercase letters.
     *
     * @param  int  $length
     * @return string
     */
    public function letters(int $length = 32): string
    {
        return $this->string($length, $lower = true, $upper = true, $numbers = false, $symbols = false, $requireAll = false);
    }

    /**
     * Generate a random string of $length which includes lowercase and uppercase letters, and numbers.
     * This is suitable for use as a random token with sufficient length, and should have a near-zero chance of collisions.
     *
     * @param  int  $length
     * @return string
     */
    public function token(int $length = 32): string
    {
        return $this->string($length, $lower = true, $upper = true, $numbers = true, $symbols = false, $requireAll = true);
    }

    /**
     * Generate a random password string of $length which includes lowercase and uppercase letters, numbers, and symbols.
     * Note, this doesn't guarantee that all the character sets will be included, you can use Random::string() for that.
     *
     * @param  int   $length
     * @param  bool  $requireAll  If true, at least one character from each set will be included.
     * @return string
     */
    public function password(int $length = 16, bool $requireAll = false): string
    {
        return $this->string($length, $lower = true, $upper = true, $numbers = true, $symbols = true, $requireAll);
    }

    /**
     * Generate a random alphanumeric string of $length divided by $delimiter.
     * This is suitable for use as a long random password that is easy to read and type.
     *
     * @param  int     $length
     * @param  string  $delimiter
     * @param  int     $chunkLength = 5
     * @param  bool    $mixedCase  If true, lowercase letters will be included.
     * @return string
     */
    public function dashed(int $length = 25, string $delimiter = '-', int $chunkLength = 5, bool $mixedCase = true): string
    {
        $string = $this->string($length, $lower = $mixedCase, $upper = true, $numbers = true, $symbols = false, $requireAll = true);

        return wordwrap($string, $chunkLength, $delimiter, true);
    }

    /**
     * Shuffle the characters in an array, string, or Laravel Collection,
     * optionally preserving the keys.
     *
     * @param  array|string|\Illuminate\Support\Collection  $values
     * @param  bool                                         $preserveKeys
     * @return array|string|\Illuminate\Support\Collection
     */
    public function shuffle($values, bool $preserveKeys = false)
    {
        if (is_string($values)) {
            return $this->randomizer->shuffleBytes($values);
        }

        if ($values instanceof Collection) {
            $shuffled = $this->shuffle($values->toArray(), $preserveKeys);
            $class = get_class($values);

            return new $class($shuffled);
        }

        if (! is_array($values)) {
            throw new \InvalidArgumentException('$value must be an array, string, or \Illuminate\Support\Collection.');
        }

        if (! $preserveKeys) {
            return $this->randomizer->shuffleArray($values);
        }

        $shuffledKeys = $this->randomizer->shuffleArray(array_keys($values));

        return array_reduce($shuffledKeys, function ($carry, $key) use ($values) {
            $carry[$key] = $values[$key];

            return $carry;
        }, []);
    }

    /**
     * Pick $count random items (or characters) from an array or Laravel Collection.
     * Returns the $count items in a new array or Collection.
     *
     * @param  array|\Illuminate\Support\Collection  $values
     * @param  int  $count                                    Number of items to pick.
     * @return array|\Illuminate\Support\Collection
     */
    public function pick($values, int $count)
    {
        if ($count < 1) {
            throw new \InvalidArgumentException('Can not pick less than one item.');
        }

        if (! is_array($values) && ! $values instanceof Collection) {
            throw new \InvalidArgumentException('$value must be an array or \Illuminate\Support\Collection.');
        }

        if ($count > count($values)) {
            throw new \InvalidArgumentException('Can not pick more than existing elements.');
        }

        $values = $this->shuffle($values);

        if ($values instanceof Collection) {
            return $values->slice(0, $count);
        }

        return array_slice($values, 0, $count);
    }

    /**
     * Picks a single item (or character) from an array, string, or Laravel Collection.
     * This is the equivalent of calling `Random::pick($values, 1)`;
     *
     * @param  array|string|\Illuminate\Support\Collection  $values
     * @return array|string|\Illuminate\Support\Collection
     */
    public function single($values)
    {
        if (is_string($values)) {
            $values = str_split($values);
        }

        $picked = $this->pick($values, 1);

        return is_array($picked) ? array_shift($picked) : $picked->first();
    }

    /**
     * @alias single()
     */
    public function pickOne($values)
    {
        return $this->single($values);
    }

    /**
     * Use custom lowercase character set for random string generation.
     *
     * @param array|string $characters
     * @return self
     */
    public function useLower($characters): self
    {
        $characters = is_array($characters)
            ? $characters
            : str_split($characters);

        $characters = array_filter($characters, 'ctype_lower');

        $this->lowerCharacters = $characters;

        return $this;
    }

    /**
     * Use custom uppercase character set for random string generation.
     *
     * @param array|string $characters
     * @return self
     */
    public function useUpper($characters): self
    {
        $characters = is_array($characters)
            ? $characters
            : str_split($characters);

        $characters = array_filter($characters, 'ctype_upper');

        $this->upperCharacters = $characters;

        return $this;
    }

    /**
     * Use custom numbers for random string generation.
     *
     * @param array|string $characters
     * @return self
     */
    public function useNumbers($characters): self
    {
        $characters = is_array($characters)
            ? $characters
            : str_split($characters);

        $characters = array_filter($characters, function ($character) {
            return $character >= 0 && $character <= 9;
        });

        $this->numberCharacters = $characters;

        return $this;
    }

    /**
     * Use custom symbol character set for random string generation.
     *
     * @param array|string $characters
     * @return self
     */
    public function useSymbols($characters): self
    {
        $this->symbolCharacters = is_array($characters)
            ? $characters
            : str_split($characters);

        return $this;
    }
}
