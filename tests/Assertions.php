<?php

namespace Tests;

trait Assertions
{
    protected function assertRegExpCustom($expression, $string, $message = '')
    {
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            return $this->assertMatchesRegularExpression($expression, $string, $message);
        }

        $this->assertRegExp($expression, $string, $message);
    }

    /**
     * Assert that a generator is actually random by confirming it doesn't keep
     * returning the same value.
     *
     * Comparing just two random draws is fragile: for a small value space (e.g.
     * picking one of 26 letters) two draws legitimately collide often enough to
     * fail CI. Sampling many draws and only failing when *every* one matches the
     * reference drives that false-positive chance down to effectively zero while
     * still catching a generator that returns a constant.
     *
     * The $reference can be a known static value (e.g. another generator's seeded
     * output) or simply a sample produced by calling the generator once.
     *
     * @param  mixed     $reference  Value the generator should differ from at least once.
     * @param  callable  $generator  Returns a comparable value (scalar or array) each call.
     */
    protected function assertVaries($reference, callable $generator, $message = '', $attempts = 50)
    {
        for ($i = 0; $i < $attempts; $i++) {
            if ($generator() !== $reference) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->fail($message !== ''
            ? $message
            : "Generator returned the same value on every one of {$attempts} attempts; expected varied results.");
    }
}
