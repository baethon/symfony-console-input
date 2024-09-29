<?php

namespace Baethon\Symfony\Console\Input\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DefaultValue
{
    public function __construct(
        public readonly string|bool|int|float|array $value,
    ) {}
}
