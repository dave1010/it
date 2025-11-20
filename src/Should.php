<?php

declare(strict_types=1);

namespace IT;

use Attribute;

#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Should
{
    public function __construct(
        public mixed $return,
        public array $with = [],
    ) {
    }
}
