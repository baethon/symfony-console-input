<?php

namespace Baethon\Symfony\Console\Input\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class Name
{
    public function __construct(public readonly string $name)
    {
        //
    }
}
