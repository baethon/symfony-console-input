<?php

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
