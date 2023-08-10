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

test('it can be created via the FormAction class', function() {

    $tester = FormAction::test(FormActionTestAction::class);

    expect($tester)->toBeInstanceOf(FormActionTester::class);

});

// the tester has a call method that will call a method on the action
test('it has a public call method, that will call methods on the action', function() {
    
    $tester = FormAction::test(FormActionTestAction::class);

    /** @var FormActionTestAction $action */
    $action = $tester->action;

    $tester->call('setFoo', ['baz']);

    expect($action->foo)->toBe('baz');
    
    $tester->call('setBar');

    expect($action->foo)->toBe('bar');
});

// the tester has an actingAs method that will set the user on the auth guard
test('it has a actingAs method, that will set the user on the auth guard', function() {
    
    $tester = FormAction::test(FormActionTestAction::class);

    $model = TestModel::create(['name' => 'test', 'email' => 'name@email.com']);

    $tester->actingAs($model);
    
    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->id)->toBe($model->id);
});

// it has a public set method, that will set data on the request, and set the request on the action
test('it has a public set method, that will set data on the request, and set the request on the action', function() {
    
    $tester = FormAction::test(FormActionTestAction::class);

    $tester->set('foo', 'baz');

    expect($tester->request->get('foo'))->toBe('baz');

    $tester->set(['foo' => 'bar', 'bar' => 'baz']);

    expect($tester->request->get('foo'))->toBe('bar');
    expect($tester->request->get('bar'))->toBe('baz');
});