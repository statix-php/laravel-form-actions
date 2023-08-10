<?php

use Statix\FormAction\FormAction;
use Illuminate\Auth\Access\AuthorizationException;

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

// the action provides a default authorize method
test('the action provides a default authorize method', function () {
    $action = new class extends FormAction
    {
        //
    };

    expect($action->authorize())->toBeTrue();
});

// the action provides a default authorize method that can be overridden
test('the action provides a default authorize method that can be overridden', function () {
    $action = new class extends FormAction
    {
        public function authorize(): bool
        {
            return false;
        }
    };

    expect($action->authorize())->toBeFalse();
});

// the action provides default failedAuthorization method, which throws an AuthorizationException
test('the action provides default failedAuthorization method, which throws an AuthorizationException', function () {
    $action = new class extends FormAction
    {
        public function authorize(): bool
        {
            return false;
        }
    };

    $action->authorizeAction();

})->throws(AuthorizationException::class);

// the action allows you to provide onFailedAuthorization callback
test('the action allows you to provide onAuthorizationFailed callback', function () {
    $action = new class extends FormAction
    {
        public function authorize(): bool
        {
            return false;
        }
    };

    $action->onFailedAuthorization(function ($action) {
        $action->set('name', 'Personal Team');
    });

    try {
        $action->authorizeAction();
    } catch (AuthorizationException $e) {
        //
    }

    expect($action->get('name'))->toBe('Personal Team');
});
