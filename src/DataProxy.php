<?php

namespace Baethon\Symfony\Console\Input;

use Symfony\Component\Console\Input\InputInterface;

/**
 * @template T
 *
 * @mixin T
 */
final class DataProxy
{
    public function __construct(
        /**
         * @var class-string<T>
         */
        private string $dataClass,

        private InputInterface $inputInterface,
    ) {}
}
