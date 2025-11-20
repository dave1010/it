<?php

declare(strict_types=1);

namespace IT\Comparator;

interface ComparatorInterface
{
    public function isEqual(mixed $expected, mixed $actual): bool;
}
