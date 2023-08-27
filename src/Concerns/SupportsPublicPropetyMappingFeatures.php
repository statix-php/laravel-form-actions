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

            if(!array_key_exists($propertyName, $validated)) {
                continue;
            }

            $value = $validated[$propertyName];
            
            if (! $inspector->propertyHasTypehints($property)) {
                $inspector->setPropertyValue($property, $value);
                
                continue;
            }
            
            $valueType = $this->getNormalizedType($value);
            $propertyTypes = $inspector->getPropertyTypeHints($property);

            if (count($propertyTypes) > 1) {

                if (in_array($valueType, $propertyTypes)) {
                    $inspector->setPropertyValue($property, $value);
                }
            } else {
                if($valueType === $propertyTypes[0]) {
                    $inspector->setPropertyValue($property, $value);
                }
            }                

        }

        return $this;
    }

    protected function getNormalizedType($value): string
    {
        $type = gettype($value);

        if($type === 'object') {
            $type = get_class($value);
        }

        if($type === 'integer') {
            $type = 'int';
        }

        return $type;
    }
}
