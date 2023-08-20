<?php

use Statix\FormAction\FormAction;

// You can toggle automatic resolution of authorization and validation to true
test('you can configure automatic authorization and validation when resolved on', function () {
    
    class TestAutoResolvingAuthorizationAction extends FormAction
    {
        public $state = false;

        public function configure(): void
        {
            $this->allowAutomaticAuthorizationValidationOnResolve();

            $this->afterValidation(function () {
                $this->state = true;
            });
        }
    };

    /** @var TestAutoResolvingAuthorizationAction $action */
    $action = app(TestAutoResolvingAuthorizationAction::class);

    expect($action->state)->toBeTrue();
});

// Or you can toggle automatic resolution of authorization and validation to false
test('you can configure automatic authorization and validation when resolved off', function () {
    
    class TestAutoResolvingAuthorizationAction2 extends FormAction
    {
        public $state = false;

        public function configure(): void
        {
            $this->disallowAutomaticAuthorizationValidationOnResolve();

            $this->afterValidation(function () {
                $this->state = true;
            });
        }
    };

    /** @var TestAutoResolvingAuthorizationAction2 $action */
    $action = app(TestAutoResolvingAuthorizationAction2::class);

    expect($action->state)->toBeFalse();
});

// by default, automatic resolution of authorization and validation is on
test('by default, automatic authorization and validation is on', function () {
    
    class TestAutoResolvingAuthorizationAction3 extends FormAction
    {
        public $state = false;

        public function configure(): void
        {
            $this->afterValidation(function () {
                $this->state = true;
            });
        }
    };

    /** @var TestAutoResolvingAuthorizationAction3 $action */
    $action = app(TestAutoResolvingAuthorizationAction3::class);

    expect($action->state)->toBeTrue();
});