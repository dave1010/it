<?php

declare(strict_types=1);

namespace Example;

use IT\Should;

#[Should(return: 4, given: [2, 2])]
function add(int $a, int $b): int
{
    return $a + $b;
}

#[Should(return: 'Hello, Dave', given: ['Dave'])]
#[Should(return: 'Hello, World')]
function greet(string $name = 'World'): string
{
    return "Hello, {$name}";
}

class Doubler
{
    #[Should(return: 8, given: [4])]
    public function timesTwo(int $value): int
    {
        return $value * 2;
    }
}

class Incrementer
{
    public function __construct(private int $step)
    {
    }

    public static function forStep1(): self
    {
        return new self(1);
    }

    #[Should(return: 2, given: [1], with: [self::class, 'forStep1'])]
    public function add(int $value): int
    {
        return $value + $this->step;
    }
}
