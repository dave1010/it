<?php

declare(strict_types=1);

namespace IT\Command;

use IT\Exception\ShouldException;
use IT\Should;
use ReflectionFunction;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class CheckCommand
{
    public function run(array $arguments): int
    {
        $paths = array_slice($arguments, 1);

        if ($paths === [] || $this->isHelpCommand($paths[0])) {
            $this->printHelp();

            return $paths === [] ? 1 : 0;
        }

        try {
            $this->loadPhpFiles($paths);
            $this->runInlineTests();
            fwrite(STDOUT, "All inline tests passed." . PHP_EOL);

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

    private function loadPhpFiles(array $paths): void
    {
        $files = [];

        foreach ($paths as $path) {
            if (is_dir($path)) {
                foreach ($this->gatherPhpFilesFromDirectory($path) as $file) {
                    $files[] = $file;
                }
                continue;
            }

            if (is_file($path) && $this->isPhpFile($path)) {
                $files[] = $path;
                continue;
            }

            throw new ShouldException(sprintf('Path "%s" is not a readable PHP file or directory.', $path));
        }

        $files = array_unique($files);

        if ($files === []) {
            throw new ShouldException('No PHP files found to inspect.');
        }

        foreach ($files as $file) {
            require_once $file;
        }
    }

    /**
     * @return iterable<string>
     */
    private function gatherPhpFilesFromDirectory(string $path): iterable
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );

        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {
            $pathname = $fileInfo->getPathname();

            if ($fileInfo->isFile() && $this->isPhpFile($pathname)) {
                yield $pathname;
            }
        }
    }

    private function isPhpFile(string $path): bool
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'php';
    }

    private function runInlineTests(): void
    {
        $userFunctions = get_defined_functions()['user'] ?? [];
        $failures = [];

        foreach ($userFunctions as $functionName) {
            $reflection = new ReflectionFunction($functionName);
            $attributes = $reflection->getAttributes(Should::class);

            foreach ($attributes as $attribute) {
                /** @var Should $test */
                $test = $attribute->newInstance();

                $actual = $reflection->invokeArgs($test->with);
                $expected = $test->return;

                $signature = sprintf(
                    '%s(%s)',
                    $functionName,
                    implode(', ', array_map(fn ($value) => $this->describe($value), $test->with))
                );

                if ($actual === $expected) {
                    fwrite(STDOUT, sprintf('Test: %s --> OK%s', $signature, PHP_EOL));
                    continue;
                }

                $failures[] = sprintf(
                    'Test: %s --> FAIL (expected %s, got %s)',
                    $signature,
                    $this->describe($expected),
                    $this->describe($actual)
                );
            }
        }

        if ($failures !== []) {
            foreach ($failures as $failure) {
                fwrite(STDERR, $failure . PHP_EOL);
            }

            throw new ShouldException('Some inline tests failed.');
        }
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

    private function printHelp(): void
    {
        $help = [
            'Usage: php bin/it <file|directory> [<file|directory> ...]',
            '',
            'Scans PHP files for functions annotated with the #[\\IT\\Should] attribute and executes them.',
            'Provide one or more files or directories to evaluate.',
            '  -h, --help, help   Show this help message',
        ];

        fwrite(STDOUT, implode(PHP_EOL, $help) . PHP_EOL);
    }
}
