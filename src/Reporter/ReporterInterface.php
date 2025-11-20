<?php

declare(strict_types=1);

namespace IT\Reporter;

use IT\Model\TestResult;

interface ReporterInterface
{
    /**
     * @param iterable<TestResult> $results
     */
    public function report(iterable $results): void;
}
