<?php

namespace Statix\FormAction\Concerns;

use Statix\FormAction\FormAction;

trait SuppportAutomaticAuthorizationValidationOnResolve
{
    protected bool $shouldAutomaticallyResolve = true;

    protected function allowAutomaticAuthorizationValidationOnResolve(): static
    {
        $this->shouldAutomaticallyResolve = true;

        return $this;
    }

    protected function disallowAutomaticAuthorizationValidationOnResolve(): static
    {
        $this->shouldAutomaticallyResolve = false;

        return $this;
    }

    protected function bootSuppportAutomaticAuthorizationValidationOnResolve(): void
    {
        if ($this->shouldAutomaticallyResolve) {

            /** @var FormAction $this */
            $this->app->resolving(static::class, function (FormAction $action) {
                $action->resolve();
            });

        }
    }

    public function resolve(): static
    {
        /** @var FormAction $this */
        $this->authorizeAction();

        $this->validateAction();

        return $this;
    }
}
