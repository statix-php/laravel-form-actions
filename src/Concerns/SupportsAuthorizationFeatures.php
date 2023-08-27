<?php

namespace Statix\FormAction\Concerns;

use Illuminate\Auth\Access\AuthorizationException;

trait SupportsAuthorizationFeatures
{
    protected bool $shouldAuthorize = true;

    public function isAuthorizationRequired(): bool
    {
        return $this->shouldAuthorize;
    }

    public function withAuthorization(): static
    {
        $this->shouldAuthorize = true;

        return $this;
    }

    public function withoutAuthorization(): static
    {
        $this->shouldAuthorize = false;

        return $this;
    }

    /**
     * The array of callbacks to run before authorization
     *
     * @var array<callable>
     */
    protected array $beforeAuthorizationCallbacks = [];

    public function beforeAuthorization(callable $callback): static
    {
        $this->beforeAuthorizationCallbacks[] = $callback;

        return $this;
    }

    public function runBeforeAuthorizationCallbacks(): static
    {
        foreach ($this->beforeAuthorizationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }

        return $this;
    }

    /**
     * The array of callbacks to run after authorization
     *
     * @var array<callable>
     */
    protected array $afterAuthorizationCallbacks = [];

    public function afterAuthorization(callable $callback): static
    {
        $this->afterAuthorizationCallbacks[] = $callback;

        return $this;
    }

    public function runAfterAuthorizationCallbacks(): static
    {
        foreach ($this->afterAuthorizationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }

        return $this;
    }

    /**
     * The array of callbacks to run after an authorization failure
     *
     * @var array<callable>
     */
    protected array $onFailedAuthorizationCallbacks = [];

    public function onFailedAuthorization(callable $callback): static
    {
        $this->onFailedAuthorizationCallbacks[] = $callback;

        return $this;
    }

    public function runOnFailedAuthorizationCallbacks(): static
    {
        foreach ($this->onFailedAuthorizationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }

        return $this;
    }

    public function failedAuthorization()
    {
        if (empty($this->onFailedAuthorizationCallbacks)) {
            throw new AuthorizationException;
        }

        $this->runOnFailedAuthorizationCallbacks();
    }

    public function authorized(): bool
    {
        return true;
    }

    public function authorize(): static
    {
        if (! $this->isAuthorizationRequired()) {
            return true;
        }

        $this->runBeforeAuthorizationCallbacks();

        $authorized = (bool) $this->app->call([$this, 'authorized']);

        if (! $authorized) {
            $this->failedAuthorization();
        }

        $this->runAfterAuthorizationCallbacks();

        return $this;
    }
}
