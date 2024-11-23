<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Option;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;

class ProxyFactory
{
    /**
     * @template T
     *
     * @param  class-string<T>  $targetDto
     * @return T
     */
    public function create(string $targetDto, InputInterface $input)
    {
        $attributes = array_filter(
            (new ReflectionClass($targetDto))->getProperties(ReflectionProperty::IS_PUBLIC),
            function (ReflectionProperty $item) {
                return $item->getAttributes(Argument::class) !== []
                    || $item->getAttributes(Option::class) !== [];
            }
        );

        $reflection = new ReflectionClass($targetDto);

        return $reflection->newLazyProxy(function () use ($attributes, $input, $targetDto) {
            $properties = [];

            foreach ($attributes as $item) {
                [$key, $value] = $this->getPropertyData($item, $input);
                $properties[$key] = $value;
            }

            return new $targetDto(...$properties);
        });
    }

    private function getPropertyData(ReflectionProperty $item, InputInterface $input): array
    {
        $option = $item->getAttributes(Option::class)[0] ?? null;
        $name = $item->getName();

        if ($option) {
            return [$name, $input->getOption($name)];
        }

        return [$name, $input->getArgument($name)];
    }
}
