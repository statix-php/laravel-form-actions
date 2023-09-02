<?php

use Illuminate\Http\Request;
use Statix\FormAction\Attributes\Computed;
use Statix\FormAction\FormAction;
use Statix\FormAction\Validation\Rules;

// test it supports using method marked as computed
test('it supports using method marked as computed', function () {
    $action = new class extends FormAction
    {
        #[Computed('getUser')]
        public stdClass $user;

        protected function getUser(): stdClass
        {
            $user = new stdClass();

            $user->name = 'John Doe';

            return $user;
        }
    };

    expect(isset($action->user))->toBeFalse();

    $action->validate();

    $action->attemptToMapValidatedDataToPublicProperties();

    expect(isset($action->user))->toBeTrue();
    expect($action->user)->toBeInstanceOf(stdClass::class);
    expect($action->user->name)->toBe('John Doe');
});

// test it supports using method marked as computed with arguments
test('it supports using method marked as computed with arguments', function () {
    $action = new class extends FormAction
    {
        #[Computed('getUser', ['Jane Doe'])]
        public stdClass $user;

        protected function getUser(string $name): stdClass
        {
            $user = new stdClass();

            $user->name = $name;

            return $user;
        }
    };

    expect(isset($action->user))->toBeFalse();

    $action->validate();

    $action->attemptToMapValidatedDataToPublicProperties();

    expect(isset($action->user))->toBeTrue();
    expect($action->user)->toBeInstanceOf(stdClass::class);
    expect($action->user->name)->toBe('Jane Doe');
});

// errors are thrown when the computed method does not exist
test('errors are thrown when the computed method does not exist', function () {
    $action = new class extends FormAction
    {
        #[Computed('getUser')]
        public stdClass $user;
    };

    expect(isset($action->user))->toBeFalse();

    $action->validate();

})->throws(Exception::class, 'The method getUser does not exist on the action.');

// test errors are thrown when the computed method does not return the correct type
test('test errors are thrown when the computed method does not return the correct type', function () {
    $action = new class extends FormAction
    {
        #[Computed('getUser')]
        public string $user;

        protected function getUser(): stdClass
        {
            $user = new stdClass();

            $user->name = 'John Doe';

            return $user;
        }
    };

    expect(isset($action->user))->toBeFalse();

    $action->validate();

})->throws(TypeError::class);

// if no method is provided, the property name is used to generate the method name
test('if no method is provided, the property name is used to generate the method name', function () {
    $action = new class extends FormAction
    {
        #[Computed]
        public stdClass $user;

        protected function getUserProperty(): stdClass
        {
            $user = new stdClass();

            $user->name = 'John Doe';

            return $user;
        }
    };

    expect(isset($action->user))->toBeFalse();

    $action->validate();

    expect(isset($action->user))->toBeTrue();
    expect($action->user)->toBeInstanceOf(stdClass::class);
    expect($action->user->name)->toBe('John Doe');
});

// the computed method can be private, but the property must be public
test('the computed method can be private, but the property must be public', function () {
    $action = new class extends FormAction
    {
        #[Computed]
        public stdClass $user;

        private function getUserProperty(): stdClass
        {
            $user = new stdClass();

            $user->name = 'John Doe';

            return $user;
        }
    };

    expect(isset($action->user))->toBeFalse();

    $action->validate();

    expect(isset($action->user))->toBeTrue();
    expect($action->user)->toBeInstanceOf(stdClass::class);
    expect($action->user->name)->toBe('John Doe');
});

// the computed method can access validated data through the validated method
test('the computed method can access validated data through the validated method', function () {
    class TestClass2 extends FormAction
    {
        #[Computed]
        public stdClass $user;

        #[Rules(['required', 'string', 'min:3', 'max:255'])]
        public string $name;

        protected function getUserProperty(): stdClass
        {
            $user = new stdClass();

            $user->name = $this->validated('name');

            return $user;
        }

        public function handle(): string
        {
            return $this->user->name;
        }
    };

    $action = new TestClass2(request: new Request([
        'name' => 'John Doe',
    ]));

    $action->validate();

    expect(isset($action->user))->toBeTrue();
    expect($action->user)->toBeInstanceOf(stdClass::class);
    expect($action->user->name)->toBe('John Doe');
    expect($action->handle())->toBe('John Doe');
});
