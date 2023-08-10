<?php

namespace Statix\FormAction;

use Illuminate\Http\Request;

/**
 * Testing API was inspired by Livewire's testing API.
 *
 * @see https://livewire.laravel.com/docs/testing
 */
class FormActionTester
{
    /**
     * The action to test.
     * 
     * @var FormAction
     */
    public $action;

    public Request $request;

    public function __construct(string $action, Request $request = null)
    {
        $this->request = $request ?? new Request;

        $this->action = new $action(request: $this->request);
    }

    public function call(string $method, array $parameters = []): static
    {
        $this->action->{$method}(...$parameters);

        return $this;
    }

    public function actingAs($user, string $driver = null): static
    {
        auth()->guard($driver)->setUser($user);

        auth()->shouldUse($driver);

        return $this;
    }

    public function set(array|string $key, mixed $value = null): static
    {
        $this->action->set($key, $value);
        
        return $this;
    }
}