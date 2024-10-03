<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Option;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class Factory
{
    public function createOptions(object|string $inputDto): array
    {
        $reflection = new ReflectionClass($inputDto);

        return array_map(
            function (ParameterDefinition $parameter) {
                $mode = 0;

                if ($parameter->option) {
                    $mode |= InputOption::VALUE_NONE;
                } else {
                    if ($parameter->required) {
                        $mode |= InputOption::VALUE_REQUIRED;
                    } else {
                        $mode |= InputOption::VALUE_OPTIONAL;
                    }
                }

                if ($parameter->list) {
                    $mode |= InputOption::VALUE_IS_ARRAY;
                }

                return new InputOption(
                    $parameter->name,
                    mode: $mode,
                    shortcut: $parameter->shortcut,
                    description: $parameter->description ?? '',
                    default: $parameter->defaultValue,
                );
            },
            $this->collectByType($reflection, Option::class),
        );
    }

    public function createArguments(object|string $inputDto): array
    {
        $reflection = new ReflectionClass($inputDto);

        return array_map(
            function (ParameterDefinition $parameter) {
                $mode = 0;

                if ($parameter->required) {
                    $mode |= InputArgument::REQUIRED;
                } else {
                    $mode |= InputArgument::OPTIONAL;
                }

                if ($parameter->list) {
                    $mode |= InputArgument::IS_ARRAY;
                }

                return new InputArgument(
                    $parameter->name,
                    mode: $mode,
                    description: $parameter->description ?? '',
                    default: $parameter->defaultValue,
                );
            },
            $this->collectByType($reflection, Argument::class),
        );
    }

    /**
     * @param  class-string  $attribute
     * @return ParameterDefinition[]
     */
    private function collectByType(ReflectionClass $reflection, string $attribute): array
    {
        $properties = array_filter(
            $reflection->getProperties(ReflectionProperty::IS_PUBLIC),
            fn (ReflectionProperty $item) => $item->getAttributes($attribute) !== [],
        );

        return array_map(
            ParameterDefinition::fromReflectionProperty(...),
            $properties,
        );
    }
}
