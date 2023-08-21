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
        } else {
            $types[] = 'required';
        }

        if ($type->isBuiltin()) {
            $types[] = $type->getName();
        } else {
            dd('TODO: Handle non-builtin types');
        }

        return $types;
    }

    public function getPropertyName(ReflectionProperty $property): string
    {
        return $property->getName();
    }

    public function hasPublicProperty(string $name): bool
    {
        return $this->reflector->hasProperty($name);
    }

    public function getPublicProperty(string $name): ReflectionProperty
    {
        return $this->reflector->getProperty($name);
    }

    public function setPublicPropertyValue(string $name, mixed $value): void
    {
        $property = $this->getPublicProperty($name);

        $property->setValue($this->object, $value);
    }

    public function doesPublicPropertyHaveDefaultValue(string $name): bool
    {
        if (! $this->hasPublicProperty($name)) {
            return false;
        }

        $property = $this->getPublicProperty($name);

        return $property->isDefault();
    }

    public function doesPropertyHaveAttributes(ReflectionProperty $property): bool
    {
        return count($property->getAttributes()) > 0;
    }

    public function getPropertyAttributes(ReflectionProperty $property): array
    {
        return $property->getAttributes();
    }

    public function getPublicPropertyDefaultValue(string $name): mixed
    {
        if (! $this->hasPublicProperty($name)) {
            return null;
        }

        $property = $this->getPublicProperty($name);

        return $property->getDefaultValue();
    }

    public function isPropertyNullable(ReflectionProperty $property): bool
    {
        $type = $property->getType();

        if (! $type) {
            return false;
        }

        return $type->allowsNull();
    }

    public function propertyHasTypehints(ReflectionProperty $property): bool
    {
        $type = $property->getType();

        if (! $type) {
            return false;
        }

        return true;
    }
}
