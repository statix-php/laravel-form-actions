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

## Usage

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
