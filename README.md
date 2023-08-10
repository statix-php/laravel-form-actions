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

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-form-actions-config"
```

This is the contents of the published config file:

```php
return [
    //
];
```

## Usage

```php
$formAction = new Statix\FormAction();
echo $formAction->echoPhrase('Hello, Statix!');
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
