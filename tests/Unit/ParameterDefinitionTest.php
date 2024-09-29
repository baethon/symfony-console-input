<?php

use Baethon\Symfony\Console\Input\Attributes\DefaultValue;
use Baethon\Symfony\Console\Input\Attributes\Description;
use Baethon\Symfony\Console\Input\Attributes\Name;
use Baethon\Symfony\Console\Input\Attributes\Shortcut;
use Baethon\Symfony\Console\Input\ParameterDefinition;

it('extracts definition using attributes', function ($dto, ParameterDefinition $expected, string $property = 'test') {
    $property = (new ReflectionClass($dto))->getProperty($property);

    expect(ParameterDefinition::fromReflectionProperty($property))
        ->toEqual($expected);
})->with([
    'required value' => [
        new class
        {
            public string $test;
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
        ),
    ],
    'optional value' => [
        new class
        {
            public ?string $test;
        },
        new ParameterDefinition(
            name: 'test',
            required: false,
        ),
    ],
    'with description' => [
        new class
        {
            #[Description('Foo')]
            public string $test;
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
            description: 'Foo',
        ),
    ],
    'with shortcut' => [
        new class
        {
            #[Shortcut('f')]
            public string $test;
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
            shortcut: 'f',
        ),
    ],
    'with name' => [
        new class
        {
            #[Name('foo')]
            public string $test;
        },
        new ParameterDefinition(
            name: 'foo',
            required: true,
        ),
    ],
    'as array' => [
        new class
        {
            public array $test;
        },
        new ParameterDefinition(
            name: 'test',
            required: true,
            list: true,
        ),
    ],
    'as option' => [
        new class
        {
            public bool $test;
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
            #[DefaultValue('Test')]
            public string $test;
        },
        new ParameterDefinition(
            name: 'test',
            required: false,
            defaultValue: 'Test',
        ),
    ],
]);
