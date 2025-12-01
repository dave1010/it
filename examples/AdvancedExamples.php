<?php

declare(strict_types=1);

namespace Example;

use IT\Should;

#[Should(return: 'first,second,third', given: [['first', 'second', 'third']])]
#[Should(return: 'first|second|third', given: [['first', 'second', 'third'], '|'])]
function joinParts(array $parts, string $separator = ','): string
{
    return implode($separator, $parts);
}

class MathUtil
{
    #[Should(return: 9, given: [3])]
    public static function square(int $value): int
    {
        return $value ** 2;
    }

    #[Should(return: 6, given: [[1, 2, 3]])]
    public static function sum(array $values): int
    {
        return array_sum($values);
    }
}

class ConfigurableIncrementer
{
    public function __construct(private int $step)
    {
    }

    public static function byThree(): self
    {
        return new self(3);
    }

    public static function makeWithThree(): self
    {
        return new self(3);
    }

    #[Should(return: 5, given: [2], with: [self::class, 'byThree'])]
    #[Should(return: 10, given: [7], with: [self::class, 'makeWithThree'])]
    public function add(int $value): int
    {
        return $value + $this->step;
    }
}
