<?php

declare(strict_types=1);

namespace IT\Command;

use IT\Exception\ShouldException;
use IT\Should;

class CheckCommand
{
    public function run(array $arguments): int
    {
        $command = $arguments[1] ?? 'should';

        if (in_array($command, ['help', '-h', '--help'], true)) {
            $this->printHelp();
            return 0;
        }

        try {
            $this->runExpectations();
            fwrite(STDOUT, "All Should expectations passed.\n");
            return 0;
        } catch (ShouldException $exception) {
            fwrite(STDERR, "Expectation failed: {$exception->getMessage()}\n");
            return 1;
        }
    }

    private function runExpectations(): void
    {
        Should::that(true, 'Truthiness check')->beTrue();
        Should::that(1 + 1, 'Math check')->equal(2);
        Should::that(false, 'Negation check')->beFalse();
    }

    private function printHelp(): void
    {
        $help = [
            'Usage: php bin/it [command]',
            '',
            'Commands:',
            '  should   Run built-in Should expectations (default)',
            '  help     Show this help message',
        ];

        fwrite(STDOUT, implode(PHP_EOL, $help) . PHP_EOL);
    }
}
