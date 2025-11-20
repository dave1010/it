<?php

declare(strict_types=1);

namespace IT\Locator;

use IT\Model\InlineTest;

interface LocatorInterface
{
    /**
     * @param list<string> $paths
     * @return list<InlineTest>
     */
    public function locate(array $paths): array;
}
