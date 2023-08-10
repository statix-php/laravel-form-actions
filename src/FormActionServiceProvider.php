<?php

namespace Statix\FormAction;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Statix\FormAction\Commands\CreateFormActionCommand;

class FormActionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-form-actions')
            ->hasConfigFile()
            ->hasCommand(CreateFormActionCommand::class);
    }
}
