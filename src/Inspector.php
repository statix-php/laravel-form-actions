<?php

namespace Statix\FormAction;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionUnionType;

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

        if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
            foreach ($type->getTypes() as $subType) {
                if ($subType->allowsNull()) {
                    $types[] = 'nullable';
                } else {
                    $types[] = 'required';
                }

                $types[] = $subType->getName();
            }
        } else {
            if ($type->allowsNull()) {
                $types[] = 'nullable';
            } else {
                $types[] = 'required';
            }

            $types[] = $type->getName();
        }

        return array_values(array_unique($types));
    }

    public function getPropertyName(ReflectionProperty $property): string
    {
        return $property->getName();
    }

    public function setPropertyValue(ReflectionProperty|string $property, mixed $value): static
    {
        if (is_string($property)) {
            if (! $this->hasProperty($property)) {
                return $this;
            }

            $property = $this->getProperty($property);
        }

        $property->setValue($this->object, $value);

        return $this;
    }

    public function doesPropertyHaveDefaultValue(ReflectionProperty|string $property): bool
    {
        if (is_string($property)) {
            if (! $this->hasProperty($property)) {
                return false;
            }

            $property = $this->getProperty($property);
        }

        return $property->isDefault();
    }

    public function doesPropertyHaveAttributes(ReflectionProperty $property, ?string $type = null): bool
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

    public function getPropertyAttributes(ReflectionProperty $property, ?string $type = null): array
    {
        if (! $type) {
            return $property->getAttributes();
        }

        $attributes = $property->getAttributes();

        return array_values(array_filter($attributes, function ($attribute) use ($type) {
            return $attribute->getName() === $type;
        }));
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

    public function getPropertyDefaultValue(ReflectionProperty|string $property): mixed
    {
        if (is_string($property)) {
            if (! $this->hasProperty($property)) {
                return null;
            }

            $property = $this->getProperty($property);
        }

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

    public function hasMethod(string $method): bool
    {
        return $this->reflector->hasMethod($method);
    }

    public function getMethod(string $method): ReflectionMethod
    {
        return $this->reflector->getMethod($method);
    }

    public function doesMethodHaveArguments(ReflectionMethod|string $method): bool
    {
        if (is_string($method) && ! $this->hasMethod($method)) {
            return false;
        }

        if (is_string($method)) {
            $method = $this->getMethod($method);
        }

        /** @var ReflectionMethod $method */
        return $method->getNumberOfParameters() > 0;
    }
}
