<?php

use Statix\FormAction\FormAction;
use Statix\FormAction\Tests\Support\TestModel;
use Statix\FormAction\Validation\Rule;

// an example use case
test('example use case 1', function () {

    $data = [
        'name' => 'John Doe',
        'email' => 'john@email.com',
    ];

    $user = TestModel::create([
        'name' => 'John Doe',
        'email' => 'user@email.com',
    ]);

    $this->actingAs($user);

    $response = $this->post('/test', $data);

    expect(TestModel::where('email', $data['email'])->exists())->toBeTrue();
});

// example use case 2
test('example use case 2', function () {

    class ExampleUseCase2 extends FormAction
    {
        #[Rule(['required', 'string', 'min:3', 'max:255'])]
        public string $name;

        #[Rule(['required', 'email'])]
        public string $email;

        public function authorized(): bool
        {
            return true;
        }

        public function handle(): TestModel
        {
            return TestModel::create([
                'name' => $this->name,
                'email' => $this->email,
            ]);
        }
    }

    $result = FormAction::test(ExampleUseCase2::class)
        ->set('name', 'John Doe')
        ->set('email', 'user@email.com')
        ->call('resolve')
        ->call('handle')
        ->thenReturn();

    expect($result->name)->toBe('John Doe');
    expect($result)->toBeInstanceOf(TestModel::class);
});
