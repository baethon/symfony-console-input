<?php

use Baethon\Symfony\Console\Input\Attributes\InputData;
use Baethon\Symfony\Console\Input\DataProxy;
use Baethon\Symfony\Console\Input\UsesInputData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Tests\Stubs\InputDataDto;

describe('configure()', function () {
    it('defines input', function () {
        $command = new class extends Command
        {
            use UsesInputData;

            #[InputData]
            private InputDataDto|DataProxy $input;
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

    it('throws exception when InputData is without proxy', function () {
        new class extends Command
        {
            use UsesInputData;

            #[InputData]
            private InputDataDto $input;
        };
    })->throws(\UnexpectedValueException::class);

    it('throws exception when InputData is just proxy', function () {
        new class extends Command
        {
            use UsesInputData;

            #[InputData]
            private DataProxy $input;
        };
    })->throws(\UnexpectedValueException::class);

    it('throws exception when InputData has three union types', function () {
        new class extends Command
        {
            use UsesInputData;

            #[InputData]
            private InputDataDto|DataProxy|\stdClass $input;
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
            public InputDataDto|DataProxy $inputData;
        };

        $input = new ArrayInput([]);

        (fn () => $this->initialize($input, new NullOutput))->call($command);

        expect($command->inputData)->toEqual(new DataProxy(InputDataDto::class, $input));
    });
});
