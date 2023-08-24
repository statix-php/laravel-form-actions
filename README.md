# Laravel Form Actions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/statix-php/laravel-form-actions.svg?style=flat-square)](https://packagist.org/packages/statix-php/laravel-form-actions)
[![Total Downloads](https://img.shields.io/packagist/dt/statix-php/laravel-form-actions.svg?style=flat-square)](https://packagist.org/packages/statix-php/laravel-form-actions)

This package is what would happen if Laravel Livewire, Spatie's Laravel Data, and Laravel's Form Requests all had a child.

## Installation

You can install this package using composer by running the command below. You can check it out on packagist by visiting this page: [https://packagist.org/packages/statix-php/laravel-form-actions](https://packagist.org/packages/statix-php/laravel-form-actions)

You can install the package via composer:

```bash
composer require statix-php/laravel-form-actions
```

## Creating `FormActions`

Similiar to [Laravel Form Requests](https://laravel.com/docs/validation#form-request-validation), you can create a new `FormAction` using `artisan` and the command below:

```bash
php artisan make:form-action ActionName
```

This will create a class called `ActionName` in the `app\Actions` directory. The contents of the default class is shown below. 

```php
<?php 

namespace App\Actions;

use Statix\FormAction\FormAction;

class ActionName extends FormAction
{
    public function authorized(): bool
    {
        return true;
    }

    public function handle()
    {
        // Do cool things, tell people - Aaron Francis
    }
}
```

Now that we have our action created, we can start fleshing it out. Let's show how we could use our action to create a new `User`. 

```php
<?php 

namespace App\Actions;

use App\Models\User;
use Statix\FormAction\FormAction;
use Statix\FormAction\Validation\Rule;

class ActionName extends FormAction
{
    #[Rule(['required', 'string', 'min:3', 'max:255'])] // string and required are explicitly added because we are not using a typehint
    public $name;

    #[Rule(['min:3', 'max:255'])] // string and required are implied with the non-nullable string typehint
    public string $email;

    // the timezone propery will automatically have the nullable and string rules applied to it based on the nullable typehint
    public ?string $timezone;

    // by default the authorized method returns true, so we could remove this method but will leave it for explicitness
    public function authorized(): bool 
    {
        return true;
    }

    // the handle method is required
    public function handle(): User
    {
        return User::create([
            'name' => $this->name,
            'email' => $this->email,
            'timezone' => $this->timezone ?? 'UTC',
        ]);
    }
}
```

Now that our action is stubbed out, lets use it in our routes.

```php
// routes/web.php

use App\Actions\ActionName;

Route::post('/register', function(ActionName $action) {

    $user = $action->handle();

    // do other stuff with the newly created user
    auth()->login($user);

    return redirect()->route('dashboard');
});
```

You can see we never manually called any authorization or validation methods. Similiar to Laravel `FormRequests`, the actions will automatically check authorization and validation when they are resolved by the container. (This behavior can be disabled).

Awesome, so now let's write some tests for this action!

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please reach out to me directly for any potential security vulnerabilities.

## Credits

- [Wyatt Castaneda](https://github.com/statix-php)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
