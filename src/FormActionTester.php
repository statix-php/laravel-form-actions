<?php

namespace Statix\FormAction;

use Illuminate\Http\Request;
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

    public function __construct(string $action, Request $request = null)
    {
        $this->request = $request ?? new Request;

        $this->action = new $action(request: $this->request);
    }

    public function actingAs($user, string $driver = null): static
    {
        auth()->guard($driver)->setUser($user);

        auth()->shouldUse($driver);

        return $this;
    }

    public function call(string $method, array $parameters = []): static
    {
        $this->action->{$method}(...$parameters);

        return $this;
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
