<?php

declare(strict_types=1);

namespace IT\Executor;

use IT\Comparator\ComparatorInterface;
use IT\Model\InlineTest;
use IT\Model\TestResult;

class InlineTestExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly ComparatorInterface $comparator,
        private readonly InlineTestInvoker $invoker,
    ) {
    }

    public function execute(iterable $tests): array
    {
        $results = [];

        foreach ($tests as $test) {
            $results[] = $this->executeTest($test);
        }

        return $results;
    }

    private function executeTest(InlineTest $test): TestResult
    {
        $actual = $this->invoker->invoke($test);
        $passed = $this->comparator->isEqual($test->getExpected(), $actual);

        return new TestResult($test, $actual, $passed);
    }
}
