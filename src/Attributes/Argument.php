<?php

namespace Baethon\Symfony\Console\Input\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Argument
{
    public function __construct()
    {
        //
    }
}
