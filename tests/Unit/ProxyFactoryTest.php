<?php

use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Name;
use Baethon\Symfony\Console\Input\ProxyFactory;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Tests\Stubs\InputDataDto;

it('creates DTO ghost instance', function () {
    $factory = new ProxyFactory;
    $input = new ArrayInput([
        '--age' => 25,
        'name' => 'Jon',
    ], new InputDefinition([
        new InputArgument('name', mode: InputArgument::REQUIRED),
        new InputOption('age', mode: InputOption::VALUE_OPTIONAL),
    ]));

    $ghost = $factory->create(InputDataDto::class, $input);

    expect($ghost->age)->toEqual(25);
    expect($ghost->name)->toEqual('Jon');
});

it('supports name attribute', function () {
    $factory = new ProxyFactory;
    $input = new ArrayInput([
        'foo' => 'Jon',
    ], new InputDefinition([
        new InputArgument('foo', mode: InputArgument::REQUIRED),
    ]));

    $dto = new class
    {
        public function __construct(
            #[Argument]
            #[Name('foo')]
            public ?string $name = null
        ) {}
    };

    $ghost = $factory->create($dto::class, $input);

    expect($ghost->name)->toEqual('Jon');
});
