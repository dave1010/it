<?php

declare(strict_types=1);

namespace IT\Model;

use ReflectionFunctionAbstract;

class InlineTest
{
    public function __construct(
        private readonly string $callableName,
        private readonly array $arguments,
        private readonly mixed $expected,
        private readonly ReflectionFunctionAbstract $reflection,
        /** @var null|callable */
        private readonly mixed $with = null,
    ) {
    }

    public function getCallableName(): string
    {
        return $this->callableName;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getExpected(): mixed
    {
        return $this->expected;
    }

    public function getReflection(): ReflectionFunctionAbstract
    {
        return $this->reflection;
    }

    public function getWith(): ?callable
    {
        return $this->with;
    }
}
