<?php

declare(strict_types=1);

namespace IT\Command;

use IT\Comparator\EqualityComparator;
use IT\Exception\ShouldException;
use IT\Executor\ExecutorInterface;
use IT\Executor\InlineTestExecutor;
use IT\Executor\InlineTestInvoker;
use IT\Locator\InlineTestFinder;
use IT\Locator\InlineTestLocator;
use IT\Locator\LocatorInterface;
use IT\Locator\PhpFileFinder;
use IT\Reporter\ReporterInterface;
use IT\Reporter\StreamReporter;

class CheckCommand
{
    private LocatorInterface $locator;
    private ExecutorInterface $executor;
    private ReporterInterface $reporter;

    public function __construct(
        ?LocatorInterface $locator = null,
        ?ExecutorInterface $executor = null,
        ?ReporterInterface $reporter = null,
    ) {
        $this->locator = $locator ?? new InlineTestLocator(new PhpFileFinder(), new InlineTestFinder());
        $this->executor = $executor ?? new InlineTestExecutor(new EqualityComparator(), new InlineTestInvoker());
        $this->reporter = $reporter ?? new StreamReporter();
    }

    public function run(array $arguments): int
    {
        $paths = array_slice($arguments, 1);

        if ($paths === [] || $this->isHelpCommand($paths[0])) {
            $this->printHelp();

            return $paths === [] ? 1 : 0;
        }

        try {
            $tests = $this->locator->locate($paths);
            $results = $this->executor->execute($tests);
            $this->reporter->report($results);

            return 0;
        } catch (ShouldException $exception) {
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);

            return 1;
        }
    }

    private function isHelpCommand(string $command): bool
    {
        return in_array($command, ['help', '-h', '--help'], true);
    }

    private function printHelp(): void
    {
        $help = [
            'Usage: php bin/it <file|directory> [<file|directory> ...]',
            '',
            'Scans PHP files for functions and methods annotated with the #[\\IT\\Should] attribute and executes them.',
            'Provide one or more files or directories to evaluate.',
            '  -h, --help, help   Show this help message',
        ];

        fwrite(STDOUT, implode(PHP_EOL, $help) . PHP_EOL);
    }
}
