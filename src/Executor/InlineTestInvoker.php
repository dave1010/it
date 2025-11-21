<?php

declare(strict_types=1);

namespace IT\Executor;

use IT\Model\InlineTest;
use ReflectionMethod;

class InlineTestInvoker
{
    public function invoke(InlineTest $test): mixed
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
