<?php

namespace Statix\FormAction\Concerns;

use Statix\FormAction\Attributes\Computed;
use Statix\FormAction\FormAction;
use Statix\FormAction\Inspector;

trait SupportsComputedAttributeFeatures
{
    protected $supportComputedProperties = true;

    public function dontSupportComputedProperties(): static
    {
        $this->supportComputedProperties = false;

        return $this;
    }

    public function supportComputedProperties(): static
    {
        $this->supportComputedProperties = true;

        return $this;
    }

    public function bootSupportsComputedAttributeFeatures(): void
    {
        if (! $this->supportComputedProperties) {
            return;
        }

        // hook into the beforeAuthorization method
        /** @var FormAction $this */
        $this->afterValidation(function (FormAction $action) {

            // get the inspector
            $inspector = Inspector::make($action);

            // get the public properties with the Computed attribute
            $publicProperties = $inspector->getPublicPropertiesWithAttribute(Computed::class);

            // loop through the public properties
            foreach ($publicProperties as $property) {

                // get the Computed attribute, it does not allow multiple so we can just get the first one

                /** @var ReflectionAttribute $computed */
                $computed = $inspector->getPropertyAttributes($property, Computed::class)[0];

                $result = $computed->newInstance()->getResult($action, $inspector->getPropertyName($property));

                // TODO: need to check that the result of the computed attribute is of the correct type and passes validation
                $inspector->setPropertyValue($property, $result);

            }

        });
    }
}
