<?php

namespace Statix\FormAction;

use Illuminate\Http\Request;
use Illuminate\Contracts\Container\Container;
use Statix\FormAction\Concerns\InteractsWithTheRequest;
use Statix\FormAction\Concerns\SupportsAuthorizationFeatures;
use Statix\FormAction\Concerns\SupportsValidationFeatures;

class FormAction
{
    use InteractsWithTheRequest,
        SupportsAuthorizationFeatures,
        SupportsValidationFeatures;

    public function __construct(protected ?Container $app = null, protected ?Request $request = null)
    {
        if (! $app) {
            $this->app = app();
        }

        if (! $request) {
            $this->request = request();
        }

        $this->setRequest($this->request);

        $this->configure();
    }

    public static function make(Container $app = null, Request $request = null): static
    {
        return new static($app, $request);
    }
    
    public static function test(string $action): FormActionTester
    {
        return new FormActionTester($action);
    }

    public function configure(): void
    {
        //
    }
}
