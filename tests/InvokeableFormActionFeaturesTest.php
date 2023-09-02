<?php

use Statix\FormAction\FormAction;
use Statix\FormAction\Validation\Rules;

// test an FormAction can have an invoke method
it('can have an invoke method', function () {
    $action = new class extends FormAction
    {
        public function __invoke()
        {
            return 'hello world';
        }
    };

    expect($action())->toBe('hello world');
});

// test an FormAction can have an invoke method with parameters
it('can have an invoke method with parameters', function () {
    $action = new class extends FormAction
    {
        public function __invoke($name)
        {
            return "hello {$name}";
        }
    };

    expect($action('world'))->toBe('hello world');
});

// test an invokeable FormAction can have a handle method, and auto resolution will still work
it('invokeable actions can still use container autoresolution', function () {
    class InvokeableFormActionWithAutoResolve extends FormAction
    {
        #[Rules(['max:255', 'min:3'])]
        public string $name;

        public function handle(): string
        {
            return $this->name;
        }

        public function __invoke(): string
        {
            return $this->handle();
        }
    }

    request()->merge(['name' => 'John Doe']);

    $action = app(InvokeableFormActionWithAutoResolve::class);

    expect($action())->toBe('John Doe');
});
