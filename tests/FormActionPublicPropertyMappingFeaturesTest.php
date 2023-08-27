<?php

use Statix\FormAction\FormAction;
use Statix\FormAction\Tests\Support\TestModel;

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

test('it supports mapping to public properties with union types', function () {
    $action = new class extends FormAction
    {
        public string|int $id;

        public function validated(string|array|int $key = null, mixed $default = null): mixed
        {
            return [
                'id' => 1,
            ];
        }
    };

    expect(isset($action->id))->toBeFalse();

    $action->attemptToMapValidatedDataToPublicProperties();

    expect(isset($action->id))->toBeTrue();
    expect($action->id)->toBe(1);

    $action = new class extends FormAction
    {
        public string|int $id;

        public function validated(string|array|int $key = null, mixed $default = null): mixed
        {
            return [
                'id' => '1',
            ];
        }
    };

    expect(isset($action->id))->toBeFalse();

    $action->attemptToMapValidatedDataToPublicProperties();

    expect(isset($action->id))->toBeTrue();
    expect($action->id)->toBe('1');
});

// test it supports non-builtin types
test('it supports non-builtin types', function () {
    $action = new class extends FormAction
    {
        public stdClass $user;

        public function validated(string|array|int $key = null, mixed $default = null): mixed
        {
            return [
                'user' => new stdClass(),
            ];
        }
    };

    expect(isset($action->user))->toBeFalse();

    $action->attemptToMapValidatedDataToPublicProperties();

    expect(isset($action->user))->toBeTrue();
    expect($action->user)->toBeInstanceOf(stdClass::class);

    $action = new class extends FormAction
    {
        public TestModel $model;

        public function validated(string|array|int $key = null, mixed $default = null): mixed
        {
            $model = new TestModel();

            return [
                'model' => $model,
            ];
        }
    };

    expect(isset($action->model))->toBeFalse();

    $action->attemptToMapValidatedDataToPublicProperties();

    expect(isset($action->model))->toBeTrue();
    expect($action->model)->toBeInstanceOf(TestModel::class);
});
