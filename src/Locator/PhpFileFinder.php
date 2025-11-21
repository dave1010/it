<?php

declare(strict_types=1);

namespace IT\Locator;

use IT\Exception\ShouldException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class PhpFileFinder
{
    /**
     * @param list<string> $paths
     * @return list<string>
     */
    public function find(array $paths): array
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
}
