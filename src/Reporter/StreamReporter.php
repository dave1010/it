<?php

declare(strict_types=1);

namespace IT\Reporter;

use IT\Exception\ShouldException;
use IT\Model\InlineTest;
use IT\Model\TestResult;

class StreamReporter implements ReporterInterface
{
    public function __construct(
        private $outputStream = STDOUT,
        private $errorStream = STDERR,
    ) {
    }

    public function report(iterable $results): void
    {
        $failures = 0;

        foreach ($results as $result) {
            $signature = $this->signature($result->getTest());

            if ($result->isPassed()) {
                fwrite($this->outputStream, sprintf('Test: %s --> OK%s', $signature, PHP_EOL));
                continue;
            }

            $failures++;
            fwrite(
                $this->errorStream,
                sprintf(
                    'Test: %s --> FAIL (expected %s, got %s)%s',
                    $signature,
                    $this->describe($result->getTest()->getExpected()),
                    $this->describe($result->getActual()),
                    PHP_EOL,
                ),
            );
        }

        if ($failures > 0) {
            throw new ShouldException('Some inline tests failed.');
        }

        fwrite($this->outputStream, 'All inline tests passed.' . PHP_EOL);
    }

    private function signature(InlineTest $test): string
    {
        return sprintf(
            '%s(%s)',
            $test->getFunctionName(),
            implode(', ', array_map(fn ($value) => $this->describe($value), $test->getArguments())),
        );
    }

    private function describe(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        return (string) (is_scalar($value) ? $value : var_export($value, true));
    }
}
