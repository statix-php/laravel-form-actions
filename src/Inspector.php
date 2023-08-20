<?php

namespace Statix\FormAction;

use ReflectionClass;
use ReflectionProperty;

class Inspector
{
    protected ReflectionClass $reflector;

    public function __construct(protected object $object)
    {
        $this->reflector = new ReflectionClass($this->object);
    }

    public static function make(object $object): static
    {
        return new static($object);
    }

    public function findPublicProperties(): array
    {
        return $this->reflector->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    public function findPublicPropertiesWhere(callable $callback): array
    {
        return array_filter($this->findPublicProperties(), $callback);
    }

    public function findPublicPropertiesWithAttribute(string $attribute): array
    {
        return array_filter(
            $this->findPublicProperties(),
            function (ReflectionProperty $property) use ($attribute) {

                $attributes = $property->getAttributes($attribute);

                return count($attributes) > 0;
            });
    }

    public function getPropertyTypeHints(ReflectionProperty $property): array
    {
        $type = $property->getType();

        if (! $type) {
            return [];
        }

        $types = [];

        if ($type->allowsNull()) {
            $types[] = 'nullable';
        }

        if ($type->isBuiltin()) {
            $types[] = $type->getName();

            return $types;
        }

        dd($type->getName());
    }

    public function getPropertyName(ReflectionProperty $property): string
    {
        return $property->getName();
    }
}
