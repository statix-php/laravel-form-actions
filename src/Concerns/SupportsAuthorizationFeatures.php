<?php 

namespace Statix\FormAction\Concerns;

trait SupportsAuthorizationFeatures
{
    protected bool $shouldAuthorize = true;

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

    public function runAfterAuthorizationCallbacks(): void
    {
        foreach ($this->afterAuthorizationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }
    }

    /**
     * The array of callbacks to run after an authorization failure
     *
     * @var array<callable>
     */
    protected array $onFailedAuthorizationCallbacks = [];

    public function runOnFailedAuthorizationCallbacks(): void
    {
        foreach ($this->onFailedAuthorizationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }
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

    public function isAuthorizationRequired(): bool
    {
        return $this->shouldAuthorize;
    }
}