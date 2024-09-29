<?php

namespace Baethon\Symfony\Console\Input\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Shortcut
{
    public function __construct(public readonly string $shortcut)
    {
        //
    }
}
