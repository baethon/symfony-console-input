<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Option;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;
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
        $factory = new LazyLoadingGhostFactory;
        $attributes = array_filter(
            (new ReflectionClass($targetDto))->getProperties(ReflectionProperty::IS_PUBLIC),
            function (ReflectionProperty $item) {
                return $item->getAttributes(Argument::class) !== []
                    || $item->getAttributes(Option::class) !== [];
            }
        );

        $initializer = function (
            GhostObjectInterface $ghostObject,
            string $method,
            array $parameters,
            &$initializer,
            array $properties
        ) use ($attributes, $input) {
            dd('foo');
            $initializer = null; // disable initialization

            foreach ($attributes as $item) {
                [$key, $value] = $this->setProperty($item, $input);
                $properties[$key] = $value;
            }

            return true;
        };

        return $factory->createProxy($targetDto, $initializer);
    }

    private function setProperty(ReflectionProperty $item, InputInterface $input): array
    {
        $option = $item->getAttributes(Option::class)[0] ?? null;
        $name = $item->getName();

        if ($option) {
            return [$name, $input->getOption($name)];
        }

        return [$name, $input->getArgument($name)];
    }
}
