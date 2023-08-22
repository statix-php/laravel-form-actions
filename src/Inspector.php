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

    public function getPublicProperties(): array
    {
        return $this->reflector->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    public function getPublicPropertiesWhere(callable $callback): array
    {
        return array_values(array_filter($this->getPublicProperties(), $callback));
    }

    public function getPublicPropertiesWithAttribute(string $attribute): array
    {
        return array_values(array_filter(
            $this->getPublicProperties(),
            function (ReflectionProperty $property) use ($attribute) {

                $attributes = $property->getAttributes($attribute);

                return count($attributes) > 0;
            }));
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

    public function doesPropertyHaveAttributes(ReflectionProperty $property, string $type = null): bool
    {
        $attributes = $property->getAttributes();

        if (count($attributes) === 0) {
            return false;
        }

        if (! $type) {
            return count($attributes) > 0;
        }

        $attributes = array_values(array_filter($attributes, function ($attribute) use ($type) {
            return $attribute->getName() === $type;
        }));

        return count($attributes) > 0;
    }

    public function getPropertyAttributes(ReflectionProperty $property, string $type = null): array
    {
        if (! $type) {
            return $property->getAttributes();
        }

        $attributes = $property->getAttributes();

        return array_values(array_filter($attributes, function ($attribute) use ($type) {
            return $attribute->getName() === $type;
        }));
    }

    public function getPublicPropertyAttributes(ReflectionProperty|string $name, string $type = null): array
    {
        if (! $this->hasPublicProperty($name)) {
            return [];
        }

        if (is_string($name)) {
            $property = $this->getPublicProperty($name);
        } else {
            $property = $name;
        }

        $attributes = $this->getPropertyAttributes($property);

        // filter by type
        if ($type) {
            $attributes = array_values(array_filter($attributes, function ($attribute) use ($type) {
                return $attribute->getName() === $type;
            }));
        }

        return $attributes;
    }

    public function hasProperty(string $name): bool
    {
        return $this->reflector->hasProperty($name);
    }

    public function hasPublicProperty(string $name): bool
    {
        if (! $this->reflector->hasProperty($name)) {
            return false;
        }

        $property = $this->getProperty($name);

        return $property->isPublic();
    }

    public function hasProtectedProperty(string $name): bool
    {
        if (! $this->reflector->hasProperty($name)) {
            return false;
        }

        $property = $this->getProperty($name);

        return $property->isProtected();
    }

    public function hasPrivateProperty(string $name): bool
    {
        if (! $this->reflector->hasProperty($name)) {
            return false;
        }

        $property = $this->getProperty($name);

        return $property->isPrivate();
    }

    public function getProperty(string $name): ReflectionProperty
    {
        return $this->reflector->getProperty($name);
    }

    public function getPropertyDefaultValue(string $name): mixed
    {
        if (! $this->hasProperty($name)) {
            return null;
        }

        $property = $this->getProperty($name);

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
