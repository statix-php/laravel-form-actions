<?php

use PHPUnit\Event\Code\Test;
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

// the action provides a default configure method
test('the action provides a default configure method', function () {
    $action = new class extends FormAction
    {
        //
    };

    expect(method_exists($action, 'configure'))->toBeTrue();
});

// the action calls the configure method on instantiation
test('the action calls the configure method on instantiation', function () {
    class TestActionNew extends FormAction
    {
        public function configure(): void
        {
            $this->withoutAuthorization();
        }
    };

    $action = TestActionNew::make();

    expect($action->isAuthorizationRequired())->toBeFalse();
});