<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\InputData;
use ReflectionIntersectionType;
use ReflectionNamedType;
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

            if (! $type) {
                throw new \UnexpectedValueException(sprintf('InputData fro property $%x needs to be typed', $item->getName()));
            }

            if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
                throw new \UnexpectedValueException(sprintf("InputData for property $%s can't be a union type or intersection", $item->getName()));
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
        $proxyFactory = new ProxyFactory;

        foreach ($properties as $item) {
            $dtoClass = $this->extractDtoClass($item->getType());
            $item->setValue($this, $proxyFactory->create($dtoClass, $input));
        }
    }

    /**
     * @return class-string
     */
    private function extractDtoClass(ReflectionNamedType $type): string
    {
        return $type->getName();
    }
}
