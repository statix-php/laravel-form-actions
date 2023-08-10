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

    protected function attemptToMapValidatedDataToPublicProperties(): void
    {
        if (! $this->mapValidatedDataToPublicProperties) {
            return;
        }

        /** @var array $validated */
        $validated = $this->validated();

        $reflector = Reflector::make($this);

        $publicProperties = $reflector->getPublicProperties();
    }
}
