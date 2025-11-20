<?php

declare(strict_types=1);

namespace Example;

use IT\Should;

#[Should(return: 4, with: [2, 2])]
function add(int $a, int $b): int
{
    return $a + $b;
}

#[Should(return: 'Hello, Dave', with: ['Dave'])]
#[Should(return: 'Hello, World')]
function greet(string $name = 'World'): string
{
    return "Hello, {$name}";
}
