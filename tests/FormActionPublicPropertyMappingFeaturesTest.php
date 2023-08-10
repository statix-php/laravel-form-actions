<?php

use Statix\FormAction\FormAction;

// test you can set the request to be used by the action
test('placeholder', function () {
    $action = new class extends FormAction
    {
        public string $name;

        public function validated(string|array|int $key = null, mixed $default = null): mixed
        {
            return [
                'name' => 'Personal Team',
            ];
        }
    };
    
    expect(isset($action->name))->toBeFalse();

    $action->attemptToMapValidatedDataToPublicProperties();

    expect(isset($action->name))->toBeTrue();    
    expect($action->name)->toBe('Personal Team');
});