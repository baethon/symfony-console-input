<?php

namespace Baethon\Symfony\Console\Input\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class Option
{
    public function __construct()
    {
        //
    }
}
