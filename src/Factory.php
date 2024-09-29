<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\Option;
use ReflectionClass;
use ReflectionProperty;
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
        return [];
    }

    /**
     * @return ParameterDefinition[]
     */
    private function collectByType(ReflectionClass $reflection, string $attribute): array
    {
        return array_map(
            ParameterDefinition::fromReflectionProperty(...),
            $reflection->getProperties(ReflectionProperty::IS_PUBLIC),
        );
    }
}
