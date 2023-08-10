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