<?php

declare(strict_types=1);

namespace IT\Model;

use ReflectionFunction;

class InlineTest
{
    public function __construct(
        private readonly string $functionName,
        private readonly array $arguments,
        private readonly mixed $expected,
        private readonly ReflectionFunction $reflection,
    ) {
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getExpected(): mixed
    {
        return $this->expected;
    }

    public function getReflection(): ReflectionFunction
    {
        return $this->reflection;
    }
}
