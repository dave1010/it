<?php

declare(strict_types=1);

namespace IT\Executor;

use IT\Comparator\ComparatorInterface;
use IT\Model\InlineTest;
use IT\Model\TestResult;
use ReflectionMethod;

class FunctionExecutor implements ExecutorInterface
{
    public function __construct(private readonly ComparatorInterface $comparator)
    {
    }

    public function execute(iterable $tests): array
    {
        $results = [];

        foreach ($tests as $test) {
            $actual = $this->invoke($test);
            $passed = $this->comparator->isEqual($test->getExpected(), $actual);

            $results[] = new TestResult($test, $actual, $passed);
        }

        return $results;
    }

    private function invoke(InlineTest $test): mixed
    {
        $reflection = $test->getReflection();

        if ($reflection instanceof ReflectionMethod) {
            return $reflection->invokeArgs($this->resolveObject($test, $reflection), $test->getArguments());
        }

        return $reflection->invokeArgs($test->getArguments());
    }

    private function resolveObject(InlineTest $test, ReflectionMethod $reflection): ?object
    {
        if ($reflection->isStatic()) {
            return null;
        }

        $factory = $test->getWith();

        if ($factory !== null) {
            return $factory();
        }

        $className = $reflection->getDeclaringClass()->getName();

        return new $className();
    }
}
