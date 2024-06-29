<?php

namespace Statix\FormAction\Attributes;

use Attribute;
use Statix\FormAction\FormAction;
use Statix\FormAction\Inspector;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Computed
{
    public function __construct(protected ?string $method = null, protected array $parameters = [])
    {
        //
    }

    public function getResult(FormAction $action, string $property): mixed
    {
        $inspector = Inspector::make($action);

        if (is_null($this->method)) {
            $this->method = 'get'.ucfirst($property).'Property';
        }

        if (! $inspector->hasMethod($this->method)) {
            throw new \Exception("The method {$this->method} does not exist on the action.");
        }

        $method = $inspector->getMethod($this->method);

        // if the method is not public, we need to set it to public
        if(! $method->isPublic()) {
            $method->setAccessible(true);
        }

        if (! $inspector->doesMethodHaveArguments($this->method)) {
            $result = $method->invoke($action);
        } else {
            $result = $method->invoke($action, ...$this->parameters);
        }

        // if the method was not public, we need to set it back to private
        if(! $method->isPublic()) {
            $method->setAccessible(false);
        }

        return $result;
    }
}
