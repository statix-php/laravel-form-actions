<?php

use Statix\FormAction\FormAction;
use Statix\FormAction\FormActionTester;
use Statix\FormAction\Tests\Support\TestModel;

class FormActionTestAction extends FormAction
{
    public $foo = 'bar';

    public function setBar()
    {
        $this->foo = 'bar';
    }

    public function setFoo($value)
    {
        $this->foo = $value;
    }
}

test('it can be created via the FormAction class', function () {

    $tester = FormAction::test(FormActionTestAction::class);

    expect($tester)->toBeInstanceOf(FormActionTester::class);

});

// the tester has a call method that will call a method on the action
test('it has a public call method, that will call methods on the action', function () {

    $tester = FormAction::test(FormActionTestAction::class);

    /** @var FormActionTestAction $action */
    $action = $tester->action;

    $tester->call('setFoo', ['baz']);

    expect($action->foo)->toBe('baz');

    $tester->call('setBar');

    expect($action->foo)->toBe('bar');
});

// the tester has an actingAs method that will set the user on the auth guard
test('it has a actingAs method, that will set the user on the auth guard', function () {

    $tester = FormAction::test(FormActionTestAction::class);

    $model = TestModel::create(['name' => 'test', 'email' => 'name@email.com']);

    $tester->actingAs($model);

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->id)->toBe($model->id);
});

// it has a public set method, that will set data on the request, and set the request on the action
test('it has a public set method, that will set data on the request, and set the request on the action', function () {

    $tester = FormAction::test(FormActionTestAction::class);

    $tester->set('foo', 'baz');

    expect($tester->request->get('foo'))->toBe('baz');

    $tester->set(['foo' => 'bar', 'bar' => 'baz']);

    expect($tester->request->get('foo'))->toBe('bar');
    expect($tester->request->get('bar'))->toBe('baz');
});

// the tester assertSet method will assert that the action has a key set, and that the value matches
test('the tester assertSet method will assert that the action has a key set, and that the value matches', function () {

    $tester = FormAction::test(FormActionTestAction::class);

    $tester->set('foo', 'bar');
    $tester->assertSet('foo', 'bar');

    $tester->set('foo', 'baz', true);
    $tester->assertSet('foo', 'baz', true);

    // test we can chain the assertSet method
    $tester
        ->assertSet('foo', 'baz')
        ->assertSet('foo', 'baz');
});

// the actingAs method works with authorization features
test('the actingAs method works with authorization features', function () {

    class TestAuthorizationAction extends FormAction
    {
        public function configure(): void
        {
            $this->afterAuthorization(function ($action) {
                $action->set('foo', 'bar');
            });
        }

        public function authorize(): bool
        {
            return auth()->check();
        }
    }

    $model = TestModel::create(['name' => 'test', 'email' => 'user@email.com']);

    FormAction::test(TestAuthorizationAction::class)
        ->actingAs($model)
        ->call('authorizeAction')
        ->assertSet('foo', 'bar');
});

// the tester can accept an object as the first argument
test('the tester can accept an object as the first argument', function () {

    $tester = FormAction::test(new class extends FormAction
    {
    });

    expect($tester->action)->toBeInstanceOf(FormAction::class);
});
