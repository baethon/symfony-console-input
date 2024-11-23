<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\Description;
use Baethon\Symfony\Console\Input\Attributes\Name;
use Baethon\Symfony\Console\Input\Attributes\Shortcut;
use ReflectionParameter;
use ReflectionType;

final class ParameterDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly bool $required = false,
        public readonly bool $list = false,
        public readonly bool $option = false,
        public readonly string|bool|int|float|array|null $defaultValue = null,
        public readonly ?string $shortcut = null,
        public readonly ?string $description = null,
    ) {
        //
    }

    public static function fromReflectionParameter(ReflectionParameter $parameter): ParameterDefinition
    {
        $type = $parameter->getType();
        $defaultValue = $parameter->isOptional()
            ? $parameter->getDefaultValue()
            : null;

        return new self(
            name: self::findAttribute($parameter, Name::class)
                ?->name ?? $parameter->getName(),
            required: match (true) {
                ! is_null($defaultValue) => false,
                $type?->allowsNull() => false,
                default => true,
            },
            description: self::findAttribute($parameter, Description::class)
                ?->description,
            shortcut: self::findAttribute($parameter, Shortcut::class)
                ?->shortcut,
            list: ($type instanceof ReflectionType && $type->getName() === 'array'),
            option: ($type instanceof ReflectionType && $type->getName() === 'bool'),
            defaultValue: $defaultValue,
        );
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $attribute
     * @return ?T
     */
    private static function findAttribute(ReflectionParameter $property, string $attribute)
    {
        $attributes = $property->getAttributes($attribute);

        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance();
    }
}
