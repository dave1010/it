<?php

declare(strict_types=1);

namespace IT\Executor;

use IT\Model\InlineTest;
use IT\Model\TestResult;

interface ExecutorInterface
{
    /**
     * @param iterable<InlineTest> $tests
     * @return list<TestResult>
     */
    public function execute(iterable $tests): array;
}
