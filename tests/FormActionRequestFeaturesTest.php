<?php

use Statix\FormAction\FormAction;

// test you can set the request to be used by the action
test('you can set the request to be used by the action', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->setRequest($request = request()->merge([
        'key' => 'value',
    ]));

    // make the request public
    $reflection = new ReflectionClass($action);
    $property = $reflection->getProperty('request');
    $property->setAccessible(true);

    expect($property->getValue($action))->toBe($request);
    expect($action->get('key'))->toBe('value');
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