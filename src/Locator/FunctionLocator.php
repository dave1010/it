<?php

declare(strict_types=1);

namespace IT\Locator;

use IT\Exception\ShouldException;
use IT\Model\InlineTest;
use IT\Should;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionFunction;
use ReflectionMethod;
use SplFileInfo;

class FunctionLocator implements LocatorInterface
{
    public function locate(array $paths): array
    {
        $files = $this->collectFiles($paths);

        foreach ($files as $file) {
            require_once $file;
        }

        return $this->collectInlineTests();
    }

    /**
     * @param list<string> $paths
     * @return list<string>
     */
    private function collectFiles(array $paths): array
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

        $files = array_values(array_unique($files));

        if ($files === []) {
            throw new ShouldException('No PHP files found to inspect.');
        }

        return $files;
    }

    /**
     * @return iterable<string>
     */
    private function gatherPhpFilesFromDirectory(string $path): iterable
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

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

    /**
     * @return list<InlineTest>
     */
    private function collectInlineTests(): array
    {
        return [
            ...$this->collectFunctionTests(),
            ...$this->collectMethodTests(),
        ];
    }

    /**
     * @return list<InlineTest>
     */
    private function collectFunctionTests(): array
    {
        $tests = [];
        $userFunctions = get_defined_functions()['user'] ?? [];

        foreach ($userFunctions as $functionName) {
            $reflection = new ReflectionFunction($functionName);
            $attributes = $reflection->getAttributes(Should::class);

            foreach ($attributes as $attribute) {
                /** @var Should $test */
                $test = $attribute->newInstance();

                $tests[] = new InlineTest(
                    $functionName,
                    $test->given,
                    $test->return,
                    $reflection,
                    $test->with,
                );
            }
        }

        return $tests;
    }

    /**
     * @return list<InlineTest>
     */
    private function collectMethodTests(): array
    {
        $tests = [];
        $classes = get_declared_classes();

        foreach ($classes as $className) {
            $reflectionClass = new \ReflectionClass($className);

            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                if ($reflectionMethod->getDeclaringClass()->getName() !== $className) {
                    continue;
                }

                $attributes = $reflectionMethod->getAttributes(Should::class);

                foreach ($attributes as $attribute) {
                    /** @var Should $test */
                    $test = $attribute->newInstance();

                    $tests[] = new InlineTest(
                        $className . '::' . $reflectionMethod->getName(),
                        $test->given,
                        $test->return,
                        $reflectionMethod,
                        $test->with,
                    );
                }
            }
        }

        return $tests;
    }
}
