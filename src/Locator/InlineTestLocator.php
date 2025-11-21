<?php

declare(strict_types=1);

namespace IT\Locator;

use IT\Model\InlineTest;

class InlineTestLocator implements LocatorInterface
{
    public function __construct(
        private readonly PhpFileFinder $fileFinder,
        private readonly InlineTestFinder $testFinder,
    ) {
    }

    /**
     * @param list<string> $paths
     * @return list<InlineTest>
     */
    public function locate(array $paths): array
    {
        $files = $this->fileFinder->find($paths);

        foreach ($files as $file) {
            require_once $file;
        }

        return $this->testFinder->collect();
    }
}
