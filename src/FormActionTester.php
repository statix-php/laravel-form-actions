<?php

namespace Statix\FormAction;

use Illuminate\Http\Request;
use Statix\FormAction\FormAction;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * Testing API was inspired by Livewire's testing API.
 *
 * @see https://livewire.laravel.com/docs/testing
 */
class FormActionTester
{
    public $action;

    public Request $request;

    protected $result;

    protected array $methodsCalled = [];

    public function __construct(string|object $action, Request $request = null)
    {
        $this->request = $request ?? new Request;

        if(is_string($action)) {
            $action = new $action(request: $this->request);
        } else {
            if(! $action instanceof FormAction) {
                throw new \Exception('The action must be an instance of FormAction');
            }

            $action->setRequest($this->request);
        }

        $this->action = $action;
    }

    public function actingAs($user, string $driver = null): static
    {
        auth()->guard($driver)->setUser($user);

        auth()->shouldUse($driver);

        return $this;
    }

    public function call(string $method, array $parameters = []): static
    {
        $this->methodsCalled[] = $method;

        if ($method === 'handle') {
            $this->result = $this->action->{$method}(...$parameters);
        } else {
            $this->action->{$method}(...$parameters);
        }

        return $this;
    }

    public function thenReturn(): mixed
    {
        return $this->result;
    }

    public function set(array|string $key, mixed $value = null, bool $replace = false): static
    {
        $this->action->set($key, $value, $replace);

        return $this;
    }

    public function assertUnauthorized(): static
    {
        // assert that the action will throw an exception in the future

        return $this;
    }

    public function assertSet($name, $value, $strict = false)
    {
        if (! $this->action->has($name)) {
            PHPUnit::fail("Failed asserting that the action has the key '{$name}'");
        }

        $actual = $this->action->get($name);

        $strict ? PHPUnit::assertSame($value, $actual) : PHPUnit::assertEquals($value, $actual);

        return $this;
    }
}
