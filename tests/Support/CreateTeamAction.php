<?php

namespace Statix\FormAction\Tests\Support;

use Statix\FormAction\FormAction;

class CreateTeamAction extends FormAction
{
    public string $name;

    public string $email;

    public function configure(): void
    {
        //
    }

    public function authorized(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:test_models,email'],
        ];
    }

    public function handle(): TestModel
    {
        return TestModel::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
