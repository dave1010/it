<?php

declare(strict_types=1);

namespace IT\Executor;

use IT\Comparator\ComparatorInterface;
use IT\Model\InlineTest;
use IT\Model\TestResult;

class FunctionExecutor implements ExecutorInterface
{
    public function __construct(private readonly ComparatorInterface $comparator)
    {
    }

    public function execute(iterable $tests): array
    {
        $results = [];

        foreach ($tests as $test) {
            $actual = $test->getReflection()->invokeArgs($test->getArguments());
            $passed = $this->comparator->isEqual($test->getExpected(), $actual);

            $results[] = new TestResult($test, $actual, $passed);
        }

        return $results;
    }
}
