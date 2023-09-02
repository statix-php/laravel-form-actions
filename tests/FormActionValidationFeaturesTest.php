<?php

use Illuminate\Validation\ValidationException;
use Statix\FormAction\FormAction;
use Statix\FormAction\Tests\Support\TestRuleUppercase;
use Statix\FormAction\Validation\Rules;

// test the public properties with Rule attributes are discovered
test('the public properties with Rule attributes are discovered', function () {
    $action = new class extends FormAction
    {
        #[Rules(['required', 'min:3', 'unique:teams,name'])]
        public string $name;

        protected array $rules = [
            'name' => 'max:255',
        ];
    };

    $rules = $action->getAllValidationRules();

    // test the rules array has a key of name
    expect(array_key_exists('name', $rules))->toBeTrue();

    // test the rules name key has a required rule, a max rule, and a string rule, test it has all three disrespective of order
    expect($rules['name'])->toContain('required', 'max:255', 'string', 'min:3', 'unique:teams,name');
});

// test the rule attribute supports object based rules
test('the rule attribute supports object based rules', function () {
    $action = new class extends FormAction
    {
        #[Rules([new TestRuleUppercase, 'min:5'])]
        public string $name;

        public function handle()
        {
            //
        }
    };

    $rules = $action->getAllValidationRules();

    // test the rules array has a key of name
    expect(array_key_exists('name', $rules))->toBeTrue();

    FormAction::test($action)
        ->set('name', 'will')
        ->call('validate');

})->expectException(ValidationException::class);

// you can toggle whether or not validation is required
test('you can toggle whether or not validation is required', function () {
    $action = new class extends FormAction
    {
        //
    };

    expect($action->isValidationRequired())->toBeTrue();

    $action->withoutValidation();

    expect($action->isValidationRequired())->toBeFalse();

    $action->withValidation();

    expect($action->isValidationRequired())->toBeTrue();
});

// you can register beforeValidation and afterValidation callbacks
test('you can register beforeValidation and afterValidation callbacks', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->beforeValidation(function ($action) {
        $action->set('name', 'Personal Team');
    });

    $action->afterValidation(function ($action) {
        $action->set('description', 'A team for personal use');
    });

    $action->runBeforeValidationCallbacks();
    $action->runAfterValidationCallbacks();

    expect($action->get('name'))->toBe('Personal Team');
    expect($action->get('description'))->toBe('A team for personal use');
});

// the action allows you to provide onFailedValidation callbacks
test('the action allows you to provide onFailedValidation callbacks', function () {
    $action = new class extends FormAction
    {
        //
    };

    $action->onFailedValidation(function ($action) {
        $action->set('name', 'Personal Team');
    });

    $action->runOnFailedValidationCallbacks();

    expect($action->get('name'))->toBe('Personal Team');
});

// the action provides a default validation rules method
test('the action provides a default validation rules method', function () {

    $action = new class extends FormAction
    {
        //
    };

    expect($action->rules())->toBe([]);
});

// the getAllValidationRules discovers rules from the rules method
test('the getAllValidationRules discovers rules from the rules method', function () {

    $action = new class extends FormAction
    {
        public function rules(): array
        {
            return [
                'name' => 'required',
            ];
        }
    };

    expect($action->getAllValidationRules())->toBe([
        'name' => 'required',
    ]);
});

// the getAllValidationRules discovers rules from the rules property
test('the getAllValidationRules discovers rules from the rules property', function () {

    $action = new class extends FormAction
    {
        public array $rules = [
            'name' => 'required',
        ];
    };

    expect($action->getAllValidationRules())->toBe([
        'name' => 'required',
    ]);
});

// the getAllValidationRules discovers rules from the rules method and property
test('the getAllValidationRules discovers rules from the rules method and property', function () {

    $action = new class extends FormAction
    {
        public $rules = [
            'name' => 'required',
        ];

        public function rules(): array
        {
            return [
                'email' => 'required',
            ];
        }
    };

    expect($action->getAllValidationRules())->toBe([
        'name' => 'required',
        'email' => 'required',
    ]);
});

// the getAllValidationRules discovers rules from the rules method and property, the method takes precedence
test('the getAllValidationRules discovers rules from the rules method and property, the method takes precedence', function () {

    $action = new class extends FormAction
    {
        public $rules = [
            'name' => 'required',
        ];

        public function rules(): array
        {
            return [
                'name' => 'required|email',
            ];
        }
    };

    expect($action->getAllValidationRules())->toBe([
        'name' => ['required', 'email'],
    ]);
});

// the action provides a default messages method
test('the action provides a default messages method', function () {

    $action = new class extends FormAction
    {
        //
    };

    expect(method_exists($action, 'messages'))->toBeTrue();
    expect($action->messages())->toBe([]);
});

// the getAllMessages discovers messages from the messages method
test('the getAllMessages discovers messages from the messages method', function () {

    $action = new class extends FormAction
    {
        public function messages(): array
        {
            return [
                'name.required' => 'The name is required',
            ];
        }
    };

    expect($action->getAllValidationMessages())->toBe([
        'name.required' => 'The name is required',
    ]);
});

// the getAllMessages discovers messages from the messages property
test('the getAllMessages discovers messages from the messages property', function () {

    $action = new class extends FormAction
    {
        public $messages = [
            'name.required' => 'The name is required',
        ];
    };

    expect($action->getAllValidationMessages())->toBe([
        'name.required' => 'The name is required',
    ]);
});

// the getAllMessages discovers messages from the messages method and property
test('the getAllMessages discovers messages from the messages method and property', function () {

    $action = new class extends FormAction
    {
        public $messages = [
            'name.required' => 'The name is required',
        ];

        public function messages(): array
        {
            return [
                'email.required' => 'The email is required',
            ];
        }
    };

    expect($action->getAllValidationMessages())->toBe([
        'name.required' => 'The name is required',
        'email.required' => 'The email is required',
    ]);
});

// the getAllMessages discovers messages from the messages method and property, the method takes precedence
test('the getAllMessages discovers messages from the messages method and property, the method takes precedence', function () {

    $action = new class extends FormAction
    {
        public $messages = [
            'name.required' => 'The name is required',
        ];

        public function messages(): array
        {
            return [
                'name.required' => 'The name is required 2',
                'email.required' => 'The email is required',
            ];
        }
    };

    expect($action->getAllValidationMessages())->toBe([
        'name.required' => 'The name is required 2',
        'email.required' => 'The email is required',
    ]);
});

// the action provides a default attributes method
test('the action provides a default attributes method', function () {

    $action = new class extends FormAction
    {
        //
    };

    expect(method_exists($action, 'attributes'))->toBeTrue();
    expect($action->attributes())->toBe([]);
});

// the getAllValidationAttributes discovers attributes from the attributes method
test('the getAllValidationAttributes discovers attributes from the attributes method', function () {

    $action = new class extends FormAction
    {
        public function attributes(): array
        {
            return [
                'name' => 'Name',
            ];
        }
    };

    expect($action->getAllValidationAttributes())->toBe([
        'name' => 'Name',
    ]);
});

// the getAllValidationAttributes discovers attributes from the attributes property
test('the getAllValidationAttributes discovers attributes from the attributes property', function () {

    $action = new class extends FormAction
    {
        public $attributes = [
            'name' => 'Name',
        ];
    };

    expect($action->getAllValidationAttributes())->toBe([
        'name' => 'Name',
    ]);
});

// the getAllValidationAttributes discovers attributes from the attributes method and property
test('the getAllValidationAttributes discovers attributes from the attributes method and property', function () {

    $action = new class extends FormAction
    {
        public $attributes = [
            'name' => 'Name',
        ];

        public function attributes(): array
        {
            return [
                'email' => 'Email',
            ];
        }
    };

    expect($action->getAllValidationAttributes())->toBe([
        'name' => 'Name',
        'email' => 'Email',
    ]);
});

// the getAllValidationAttributes discovers attributes from the attributes method and property, the method takes precedence
test('the getAllValidationAttributes discovers attributes from the attributes method and property, the method takes precedence', function () {

    $action = new class extends FormAction
    {
        public $attributes = [
            'name' => 'Name',
        ];

        public function attributes(): array
        {
            return [
                'name' => 'Name 2',
                'email' => 'Email',
            ];
        }
    };

    expect($action->getAllValidationAttributes())->toBe([
        'name' => 'Name 2',
        'email' => 'Email',
    ]);
});
