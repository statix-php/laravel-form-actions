<?php

namespace Statix\FormAction\Concerns;

use ReflectionProperty;
use Statix\FormAction\Inspector;

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

    public function attemptToMapValidatedDataToPublicPropertiesOld(): static
    {
        if (! $this->mapValidatedDataToPublicProperties) {
            return $this;
        }

        /** @var array $validated */
        $validated = $this->validated();

        $inspector = Inspector::make($this);

        $publicProperties = $inspector->getPublicProperties();

        // loop through the public properties
        foreach ($publicProperties as $property) {
            // if the validated data has a key that matches the property name
            if (array_key_exists($property->getName(), $validated)) {
                $value = $validated[$property->getName()];

                // need to handle union types
                $valueType = gettype($value);

                $propertyTypes = $inspector->getPropertyTypeHints($property);

                // if the property has a union type
                if (count($propertyTypes) > 1) {
                    // loop through the types
                    foreach ($propertyTypes as $propertyType) {
                        // if the validated data type matches one of the property types
                        if ($valueType == $propertyType->getName()) {
                            // set the property value
                            $this->{$property->getName()} = $validated[$property->getName()];
                        }
                    }
                } else {
                    // if the property type and the validated data type match
                    if (gettype($value) == $property->getType()->getName()) {
                        $this->{$property->getName()} = $validated[$property->getName()];
                    } else {
                        throw new \Exception('The type of the validated data does not match the type of the property.');
                    }
                }
            }
        }

        return $this;
    }

    public function attemptToMapValidatedDataToPublicProperties(): static
    {
        if (! $this->mapValidatedDataToPublicProperties) {
            return $this;
        }

        /** @var array $validated */
        $validated = $this->validated();

        $inspector = Inspector::make($this);

        $publicProperties = $inspector->getPublicProperties();

        /** @var ReflectionProperty $property */
        foreach ($publicProperties as $property) {

            $propertyName = $inspector->getPropertyName($property);

            if (array_key_exists($propertyName, $validated)) {

                $value = $validated[$propertyName];

                if(!$inspector->propertyHasTypehints($property)) {
                    $inspector->setPropertyValue($property, $value);

                    continue;
                }

                $propertyTypes = $inspector->getPropertyTypeHints($property);

                if (count($propertyTypes) > 1) {
                    
                    $valueType = gettype($value);

                    if($valueType === 'integer') {
                        $valueType = 'int';
                    }

                    if(in_array($valueType, $propertyTypes)) {
                        $inspector->setPropertyValue($property, $value);
                    }
                } else {
                    if (gettype($value) === $propertyTypes[0]->getName()) {
                        $inspector->setPropertyValue($property, $value);
                    } else {
                        throw new \Exception('The type of the validated data does not match the type of the property.');
                    }
                }

            }
        }

        return $this;
    }

}
