<?php

use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Description;
use Baethon\Symfony\Console\Input\Attributes\Option;
use Baethon\Symfony\Console\Input\Attributes\Shortcut;
use Baethon\Symfony\Console\Input\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

describe('InputOption', function () {
    it('creates list of options', function ($inputDto, array $expected) {
        $actual = (new Factory)->createOptions($inputDto);
        expect($actual)->toEqual($expected);
    })->with([
        'boolean flag' => [
            new class(true)
            {
                public function __construct(
                    #[Option]
                    public bool $test,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_NONE),
            ],
        ],

        'string required option' => [
            new class('')
            {
                public function __construct(
                    #[Option]
                    public string $test,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_REQUIRED),
            ],
        ],

        'string option' => [
            new class('')
            {
                public function __construct(
                    #[Option]
                    public ?string $test,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_OPTIONAL),
            ],
        ],

        'array required option' => [
            new class([])
            {
                public function __construct(
                    #[Option]
                    public array $test,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY),
            ],
        ],

        'array option' => [
            new class([])
            {
                public function __construct(
                    #[Option]
                    public ?array $test,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY),
            ],
        ],

        'shortcut' => [
            new class(true)
            {
                public function __construct(
                    #[Option]
                    #[Shortcut('t')]
                    public bool $test,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_NONE, shortcut: 't'),
            ],
        ],

        'description' => [
            new class(true)
            {
                public function __construct(
                    #[Option]
                    #[Description('Lorem ipsum')]
                    public bool $test,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_NONE, description: 'Lorem ipsum'),
            ],
        ],

        'multiple options' => [
            new class(true, '', [])
            {
                public function __construct(
                    #[Option]
                    public bool $test,

                    #[Option]
                    public ?string $name,

                    #[Option]
                    public array $roles,
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_NONE),
                new InputOption('name', mode: InputOption::VALUE_OPTIONAL),
                new InputOption('roles', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY),
            ],
        ],

        'default value' => [
            new class('')
            {
                public function __construct(
                    #[Option]
                    public string $test = 'Lorem ipsum',
                ) {}
            },
            [
                new InputOption('test', mode: InputOption::VALUE_OPTIONAL, default: 'Lorem ipsum'),
            ],
        ],
    ]);

    it('doesnt pick non-options', function () {
        $inputDto = new class('', '')
        {
            public function __construct(
                #[Argument]
                public string $test,

                public string $foo,
            ) {}
        };

        $actual = (new Factory)->createOptions($inputDto);
        expect($actual)->toBe([]);
    });
});

describe('InputArgument', function () {
    it('creates list of arguments', function ($inputDto, array $expected) {
        $actual = (new Factory)->createArguments($inputDto);
        expect($actual)->toEqual($expected);
    })->with([
        'string required option' => [
            new class('')
            {
                public function __construct(
                    #[Argument]
                    public string $test,
                ) {}
            },
            [
                new InputArgument('test', mode: InputArgument::REQUIRED),
            ],
        ],

        'string option' => [
            new class('')
            {
                public function __construct(
                    #[Argument]
                    public ?string $test,
                ) {}
            },
            [
                new InputArgument('test', mode: InputArgument::OPTIONAL),
            ],
        ],

        'array required option' => [
            new class([])
            {
                public function __construct(
                    #[Argument]
                    public array $test,
                ) {}
            },
            [
                new InputArgument('test', mode: InputArgument::REQUIRED | InputArgument::IS_ARRAY),
            ],
        ],

        'array option' => [
            new class([])
            {
                public function __construct(
                    #[Argument]
                    public ?array $test,
                ) {}
            },
            [
                new InputArgument('test', mode: InputArgument::OPTIONAL | InputArgument::IS_ARRAY),
            ],
        ],

        'description' => [
            new class('')
            {
                public function __construct(
                    #[Argument]
                    #[Description('Lorem ipsum')]
                    public string $test,
                ) {}
            },
            [
                new InputArgument('test', mode: InputArgument::REQUIRED, description: 'Lorem ipsum'),
            ],
        ],

        'multiple options' => [
            new class('', '', [])
            {
                public function __construct(
                    #[Argument]
                    public string $test,

                    #[Argument]
                    public ?string $name,

                    #[Argument]
                    public array $roles,
                ) {}
            },
            [
                new InputArgument('test', mode: InputArgument::REQUIRED),
                new InputArgument('name', mode: InputArgument::OPTIONAL),
                new InputArgument('roles', mode: InputArgument::REQUIRED | InputArgument::IS_ARRAY),
            ],
        ],

        'default value' => [
            new class('')
            {
                public function __construct(
                    #[Argument]
                    public string $test = 'Lorem ipsum',
                ) {}
            },
            [
                new InputArgument('test', mode: InputArgument::OPTIONAL, default: 'Lorem ipsum'),
            ],
        ],
    ]);

    it('doesnt pick non-arguments', function () {
        $inputDto = new class('', '')
        {
            public function __construct(
                #[Option]
                public string $test,

                public string $bar,
            ) {}
        };

        $actual = (new Factory)->createArguments($inputDto);
        expect($actual)->toBe([]);
    });
});
