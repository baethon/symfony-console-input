<?php

namespace Baethon\Symfony\Console\Input;

use Baethon\Symfony\Console\Input\Attributes\Description;
use Baethon\Symfony\Console\Input\Attributes\Name;
use Baethon\Symfony\Console\Input\Attributes\Shortcut;
use ReflectionProperty;
use ReflectionType;

final class ParameterDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly bool $required = false,
        public readonly bool $list = false,
        public readonly ?string $shortcut = null,
        public readonly ?string $description = null,
    ) {
        //
    }

    public static function fromReflectionProperty(ReflectionProperty $property): ParameterDefinition
    {
        $type = $property->getType();

        return new self(
            name: self::findAttribute($property, Name::class)
                ?->name ?? $property->getName(),
            required: match ($type?->allowsNull()) {
                null, false => true,
                default => false,
            },
            description: self::findAttribute($property, Description::class)
                ?->description,
            shortcut: self::findAttribute($property, Shortcut::class)
                ?->shortcut,
            list: ($type instanceof ReflectionType && $type->getName() === 'array'),
        );
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $attribute
     * @return ?T
     */
    private static function findAttribute(ReflectionProperty $property, string $attribute)
    {
        $attributes = $property->getAttributes($attribute);

        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance();
    }
}
