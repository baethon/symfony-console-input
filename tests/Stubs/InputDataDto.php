<?php

namespace Tests\Stubs;

use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Option;

readonly class InputDataDto
{
    public function __construct(
        #[Argument]
        public string $name,

        #[Option]
        public int $age = 18,
    ) {}
}
