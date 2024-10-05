<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\InputData;
use ReflectionObject;
use ReflectionProperty;
use ReflectionUnionType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @mixin Command
 */
trait UsesInputData
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->configureInputData();
    }

    final protected function configureInputData(): void
    {
        $properties = $this->findInputData(validate: true);
        $factory = new Factory;

        foreach ($properties as $item) {
            $dtoClass = $this->extractDtoClass($item->getType());

            if (! $dtoClass) {
                throw new \UnexpectedValueException('Unable to find DTO class');
            }

            $this->getDefinition()->addArguments($factory->createArguments($dtoClass));
            $this->getDefinition()->addOptions($factory->createOptions($dtoClass));
        }
    }

    /**
     * @return ReflectionProperty[]
     */
    private function findInputData(bool $validate = false): array
    {
        $reflection = new ReflectionObject($this);

        $inputData = array_filter(
            $reflection->getProperties(),
            function (ReflectionProperty $item) {
                return $item->getAttributes(InputData::class) !== [];
            }
        );

        if (! $validate) {
            return $inputData;
        }

        foreach ($inputData as $item) {
            $type = $item->getType();

            if (! $type instanceof ReflectionUnionType) {
                throw new \UnexpectedValueException(sprintf('InputData for property $%s needs to be a union type', $item->getName()));
            }

            $subtypes = $type->getTypes();

            if (count($subtypes) !== 2) {
                throw new \UnexpectedValueException(sprintf('The union type for property $%s should have exactly two types', $reflection->getName()));
            }

            $hasProxy = array_filter($subtypes, function ($reflection) {
                return ($reflection instanceof \ReflectionNamedType) && $reflection->getName() === DataProxy::class;
            }) !== [];

            if (! $hasProxy) {
                throw new \UnexpectedValueException(sprintf('Property $%s needs to be a union type with DataProxy', $item->getName()));
            }
        }

        return $inputData;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->initializeInputData($input);
    }

    final protected function initializeInputData(InputInterface $input): void
    {
        $properties = $this->findInputData();

        foreach ($properties as $item) {
            $dtoClass = $this->extractDtoClass($item->getType());
            $item->setValue($this, new DataProxy($dtoClass, $input));
        }
    }

    /**
     * @return class-string
     */
    private function extractDtoClass(\ReflectionUnionType $type): string
    {
        $dtoType = array_filter(
            $type->getTypes(),
            fn (\ReflectionNamedType $type) => $type->getName() !== DataProxy::class
        );

        $dtoClass = current($dtoType);

        return $dtoClass->getName();
    }
}
