# Laravel Form Actions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/statix-php/laravel-form-actions.svg?style=flat-square)](https://packagist.org/packages/statix-php/laravel-form-actions)
[![Total Downloads](https://img.shields.io/packagist/dt/statix-php/laravel-form-actions.svg?style=flat-square)](https://packagist.org/packages/statix-php/laravel-form-actions)

Laravel Form Actions combines the best features of Spatie's Laravel Data and Laravel's Form Requests, resulting in a powerful and efficient package that simplifies form handling in your Laravel applications.

## Installation

You can easily install this package using Composer by running the following command. For more details, visit the [Packagist page](https://packagist.org/packages/statix-php/laravel-form-actions).

```bash
composer require statix-php/laravel-form-actions
```

## Creating `FormActions`

Similiar to [Laravel Form Requests](https://laravel.com/docs/validation#form-request-validation), you can create a new `FormAction` using Artisan with the following command:

```bash
php artisan make:form-action ActionName
```

This command will generate a ActionName class in the app\Actions directory. The initial content of the class is as follows:

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

Once the action is created, you can start building it out. Let's demonstrate how to use an action to create a new `User`.

```php
<?php 

namespace App\Actions;

use App\Models\User;
use Statix\FormAction\FormAction;
use Statix\FormAction\Validation\Rule;

class ActionName extends FormAction
{
    #[Rule(['required', 'string', 'min:3', 'max:255'])] 
    public $name;

    #[Rule(['email', 'unique:users,email'])] 
    public string $email;

    public ?string $timezone;

    public function authorized(): bool 
    {
        return true;
    }

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

With the action in place, let's integrate it into our routes.

```php
// routes/web.php

use App\Actions\ActionName;

Route::post('/register', function(ActionName $action) {

    $user = $action->handle();

    auth()->login($user);

    return redirect()->route('dashboard');
});
```

No manual authorization or validation calls are required. Just like Laravel `FormRequest`, the actions automatically handle authorization and validation when they're resolved from the container. (This behavior can be disabled).

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
