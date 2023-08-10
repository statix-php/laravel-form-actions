<?php

use Statix\FormAction\FormAction;

// test the action can be instantiated
test('the action can be instantiated', function () {
    $action = new class extends FormAction
    {
        //
    };

    expect($action)->toBeInstanceOf(FormAction::class);
});

// the action can be instantiated using the make method
test('the action can be instantiated using the make method', function () {
    class TestAction extends FormAction
    {
        //
    };

    $action = TestAction::make();

    expect($action)->toBeInstanceOf(FormAction::class);
});

// test the action has the ability to set additional data in the request
test('the action can set additional data in the request via the set method', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->set('name', 'Personal Team');

    expect($action->has('name'))->toBeTrue();
    expect($action->get('name'))->toBe('Personal Team');
});

// the action can set additional data in the request via the set method using an array
test('the action can set additional data in the request via the set method using an array', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->set([
        'name' => 'Personal Team',
        'description' => 'A team for personal use',
    ]);

    expect($action->get('name'))->toBe('Personal Team');
    expect($action->get('description'))->toBe('A team for personal use');
});

// test the action has the ability to set additional data in the request using a callback
test('the action can set additional data in the request using a callback', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->set('name', fn () => 'Personal Team');

    expect($action->get('name'))->toBe('Personal Team');

    class Service
    {
        public function name(): string
        {
            return 'Personal Team 2';
        }
    }

    $action->set('name', function(Service $service) {
        return $service->name();
    }, true);

    expect($action->get('name'))->toBe('Personal Team 2');
});

// by default the set method will not replace existing data
test('by default the set method will not replace existing data', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->set('name', 'Personal Team');
    $action->set('name', 'Personal Team 2');

    expect($action->get('name'))->toBe('Personal Team');
});

// the set method can replace existing data
test('the set method can replace existing data', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->set('name', 'Personal Team');
    $action->set('name', 'Personal Team 2', true);

    expect($action->get('name'))->toBe('Personal Team 2');
});

// You can toggle whether or not authorization is required
test('you can toggle whether or not authorization is required', function () {
    $action = new class extends FormAction
    {
        //
    };

    expect($action->isAuthorizationRequired())->toBeTrue();

    $action->withoutAuthorization();

    expect($action->isAuthorizationRequired())->toBeFalse();

    $action->withAuthorization();

    expect($action->isAuthorizationRequired())->toBeTrue();
});

// you can register beforeAuthorization and afterAuthorization callbacks
test('you can register beforeAuthorization and afterAuthorization callbacks', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->beforeAuthorization(function ($action) {
        $action->set('name', 'Personal Team');
    });

    $action->afterAuthorization(function ($action) {
        $action->set('description', 'A team for personal use');
    });

    $action->runBeforeAuthorizationCallbacks();
    $action->runAfterAuthorizationCallbacks();

    expect($action->get('name'))->toBe('Personal Team');
    expect($action->get('description'))->toBe('A team for personal use');
});