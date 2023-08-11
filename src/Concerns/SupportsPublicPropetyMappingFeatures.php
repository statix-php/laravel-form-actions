<?php

namespace Statix\FormAction\Concerns;

use Statix\FormAction\Reflector;

trait SupportsPublicPropetyMappingFeatures
{
    protected bool $mapValidatedDataToPublicProperties = true;

    public function dontMapValidatedDataToPublicProperties(): static
    {
        $this->mapValidatedDataToPublicProperties = false;

        return $this;
    }

    public function mapValidatedDataToPublicProperties(): static
    {
        $this->mapValidatedDataToPublicProperties = true;

        return $this;
    }

    public function attemptToMapValidatedDataToPublicProperties(): static
    {
        if (! $this->mapValidatedDataToPublicProperties) {
            return $this;
        }

        /** @var array $validated */
        $validated = $this->validated();

        $reflector = Reflector::make($this);

        $publicProperties = $reflector->getPublicProperties();

        // loop through the public properties 
        foreach ($publicProperties as $property) {
            // if the validated data has a key that matches the property name
            if (array_key_exists($property->getName(), $validated)) {
                $value = $validated[$property->getName()];

                // check if the property type and the validated data type match
                if (gettype($value) == $property->getType()->getName()) {
                    $this->{$property->getName()} = $validated[$property->getName()];
                } else {
                    throw new \Exception("The type of the validated data does not match the type of the property.");
                }
            }
        }

        return $this;
    }
}
