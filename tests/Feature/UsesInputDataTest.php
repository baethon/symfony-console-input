<?php

use Baethon\Symfony\Console\Input\Attributes\InputData;
use Baethon\Symfony\Console\Input\UsesInputData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Stubs\InputDataDto;

describe('configure()', function () {
    it('defines input', function () {
        $command = new class extends Command
        {
            use UsesInputData;

            #[InputData]
            private InputDataDto $input;
        };

        expect($command->getDefinition()->getArguments())->toEqual([
            'name' => new InputArgument(
                'name',
                mode: InputArgument::REQUIRED
            ),
        ]);
        expect($command->getDefinition()->getOptions())->toEqual([
            'age' => new InputOption(
                'age',
                mode: InputOption::VALUE_OPTIONAL,
                default: 18,
            ),
        ]);
    });

    it('throws exception when InputData is union type', function () {
        new class extends Command
        {
            use UsesInputData;

            #[InputData]
            private InputDataDto|\stdClass $input;
        };
    })->throws(\UnexpectedValueException::class);

    it('throws exception when InputData has no type', function () {
        new class extends Command
        {
            use UsesInputData;

            #[InputData]
            private $input;
        };
    })->throws(\UnexpectedValueException::class);
});

describe('initialize()', function () {
    it('sets data proxy during initialization', function () {
        $command = new class extends Command
        {
            use UsesInputData;

            #[InputData]
            public InputDataDto $inputData;
        };

        $input = new ArrayInput([
            '--age' => 25,
            'name' => 'Jon',
        ], new InputDefinition([
            new InputArgument('name', mode: InputArgument::REQUIRED),
            new InputOption('age', mode: InputOption::VALUE_OPTIONAL),
        ]));

        (fn () => $this->initialize($input, new NullOutput))->call($command);

        dd($command->inputData->age);

        expect($command->inputData)->toBeInstanceOf(InputDataDto::class);
        expect($command->inputData->name)->toEqual('Jon');
        expect($command->inputData->age)->toEqual(25);
    });
});
