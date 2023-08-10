<?php

namespace Statix\FormAction;

use ReflectionClass;

class Reflector
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
        return $this->reflector->getProperties(\ReflectionProperty::IS_PUBLIC);
    }
}
