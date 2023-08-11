<?php

use Statix\FormAction\Tests\Support\TestModel;

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
