<?php

use Baethon\Symfony\Console\Input\Attributes\DefaultValue;
use Baethon\Symfony\Console\Input\Attributes\Description;
use Baethon\Symfony\Console\Input\Attributes\Option;
use Baethon\Symfony\Console\Input\Attributes\Shortcut;
use Baethon\Symfony\Console\Input\Factory;
use Symfony\Component\Console\Input\InputOption;

it('creates list of options', function ($inputDto, array $expected) {
    $actual = (new Factory)->createOptions($inputDto);
    expect($actual)->toEqual($expected);
})->with([
    'boolean flag' => [
        new class
        {
            #[Option]
            public bool $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_NONE),
        ],
    ],

    'string required option' => [
        new class
        {
            #[Option]
            public string $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_REQUIRED),
        ],
    ],

    'string option' => [
        new class
        {
            #[Option]
            public ?string $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_OPTIONAL),
        ],
    ],

    'array required option' => [
        new class
        {
            #[Option]
            public array $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY),
        ],
    ],

    'array option' => [
        new class
        {
            #[Option]
            public ?array $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY),
        ],
    ],

    'shortcut' => [
        new class
        {
            #[Option]
            #[Shortcut('t')]
            public bool $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_NONE, shortcut: 't'),
        ],
    ],

    'description' => [
        new class
        {
            #[Option]
            #[Description('Lorem ipsum')]
            public bool $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_NONE, description: 'Lorem ipsum'),
        ],
    ],

    'multiple options' => [
        new class
        {
            #[Option]
            public bool $test;

            #[Option]
            public ?string $name;

            #[Option]
            public array $roles;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_NONE),
            new InputOption('name', mode: InputOption::VALUE_OPTIONAL),
            new InputOption('roles', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY),
        ],
    ],

    'default value' => [
        new class
        {
            #[Option]
            #[DefaultValue('Lorem ipsum')]
            public string $test;
        },
        [
            new InputOption('test', mode: InputOption::VALUE_OPTIONAL, default: 'Lorem ipsum'),
        ],
    ],
]);
