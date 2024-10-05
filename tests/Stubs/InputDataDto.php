<?php

namespace Tests\Stubs;

use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Option;

class InputDataDto
{
    #[Argument]
    public string $name;

    #[Option]
    public int $age = 18;
}
