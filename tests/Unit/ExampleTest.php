<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Str;

use function PHPUnit\Framework\assertIsString;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function does_string_returns_name_of_a_method()
    {
        $str = Str::random(12);
        return assertIsString($str);
    }
}
