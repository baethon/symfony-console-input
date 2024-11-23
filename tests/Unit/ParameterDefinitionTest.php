<?php

use Baethon\Symfony\Console\Input\Attributes\Description;
use Baethon\Symfony\Console\Input\Attributes\Name;
use Baethon\Symfony\Console\Input\Attributes\Shortcut;
use Baethon\Symfony\Console\Input\ParameterDefinition;

it('extracts definition using attributes', function ($dto, ParameterDefinition $expected, string $property = 'test') {
    $constructor = (new ReflectionClass($dto))->getConstructor();
    $parameter = array_find(
        $constructor->getParameters(),
        fn (ReflectionParameter $item) => $item->getName() === $property
    );

    expect(ParameterDefinition::fromReflectionParameter($parameter))
        ->toEqual($expected);
})->with([
    'required value' => [
        new class('')
        {
            public function __construct(
                public string $test,
            ) {}
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
        ),
    ],
    'optional value' => [
        new class(null)
        {
            public function __construct(
                public ?string $test,
            ) {}
        },
        new ParameterDefinition(
            name: 'test',
            required: false,
        ),
    ],
    'with description' => [
        new class('')
        {
            public function __construct(
                #[Description('Foo')]
                public string $test,
            ) {}
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
            description: 'Foo',
        ),
    ],
    'with shortcut' => [
        new class('')
        {
            public function __construct(
                #[Shortcut('f')]
                public string $test,
            ) {}
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
            shortcut: 'f',
        ),
    ],
    'with name' => [
        new class('')
        {
            public function __construct(
                #[Name('foo')]
                public string $test,
            ) {}
        },
        new ParameterDefinition(
            name: 'foo',
            required: true,
        ),
    ],
    'as array' => [
        new class([])
        {
            public function __construct(
                public array $test,
            ) {}
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
            list: true,
        ),
    ],
    'as option' => [
        new class(true)
        {
            public function __construct(
                public bool $test,
            ) {}
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
            option: true,
        ),
    ],
    'default value' => [
        new class
        {
            public function __construct(
                public string $test = 'Test',
            ) {}
        },
        new ParameterDefinition(
            name: 'test',
            required: false,
            defaultValue: 'Test',
        ),
    ],
]);
