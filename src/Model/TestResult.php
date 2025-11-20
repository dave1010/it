<?php

declare(strict_types=1);

namespace IT\Model;

class TestResult
{
    public function __construct(
        private readonly InlineTest $test,
        private readonly mixed $actual,
        private readonly bool $passed,
    ) {
    }

    public function getTest(): InlineTest
    {
        return $this->test;
    }

    public function getActual(): mixed
    {
        return $this->actual;
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }
}
