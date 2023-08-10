<?php

namespace Statix\FormAction\Concerns;

use Illuminate\Validation\Validator;

trait SupportsValidationFeatures
{
    protected Validator $validator;

    protected bool $didValidationPass = false;

    public function validated(string|array|int $key = null, mixed $default = null): mixed
    {
        if (! isset($this->validator) && $this->validator instanceof Validator && ! $this->didValidationPass) {
            throw new \Exception('The validator must be set before calling validated()');
        }

        return data_get($this->validator->validated(), $key, $default);
    }

    protected bool $shouldValidate = true;

    public function isValidationRequired(): bool
    {
        return $this->shouldValidate;
    }

    public function withValidation(): static
    {
        $this->shouldValidate = true;

        return $this;
    }

    public function withoutValidation(): static
    {
        $this->shouldValidate = false;

        return $this;
    }

    protected bool $shouldStopOnFirstFailure = false;

    public function stopOnFirstFailure(): static
    {
        $this->shouldStopOnFirstFailure = true;

        return $this;
    }

    public function dontStopOnFirstFailure(): static
    {
        $this->shouldStopOnFirstFailure = false;

        return $this;
    }

    /**
     * The array of callbacks to run before validation
     *
     * @var array<callable>
     */
    protected array $beforeValidationCallbacks = [];

    public function beforeValidation(callable $callback): static
    {
        $this->beforeValidationCallbacks[] = $callback;

        return $this;
    }

    public function runBeforeValidationCallbacks(): static
    {
        foreach ($this->beforeValidationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }

        return $this;
    }

    /**
     * The array of callbacks to run after validation
     *
     * @var array<callable>
     */
    protected array $afterValidationCallbacks = [];

    public function afterValidation(callable $callback): static
    {
        $this->afterValidationCallbacks[] = $callback;

        return $this;
    }

    public function runAfterValidationCallbacks(): static
    {
        foreach ($this->afterValidationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }

        return $this;
    }

    /**
     * The array of callbacks to run after a validation failure
     *
     * @var array<callable>
     */
    protected array $onFailedValidationCallbacks = [];

    public function onFailedValidation(callable $callback): static
    {
        $this->onFailedValidationCallbacks[] = $callback;

        return $this;
    }

    public function runOnFailedValidationCallbacks(): static
    {
        foreach ($this->onFailedValidationCallbacks as $callback) {
            $this->app->call($callback, ['action' => $this]);
        }

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rule Features
    |--------------------------------------------------------------------------
    */
    public function getAllValidationRules(): array
    {
        return array_merge(
            $this->getRulesFromTheRulesProperty(),
            $this->getRulesFromTheRulesMethod(),
            $this->getRulesFromRuleAttributesOnPublicProperties(),
        );
    }

    private function getRulesFromTheRulesProperty(): array
    {
        if (isset($this->rules) && is_array($this->rules)) {
            return $this->rules;
        }

        return [];
    }

    private function getRulesFromTheRulesMethod(): array
    {
        $rules = $this->app->call([$this, 'rules'], ['action' => $this]);

        if (! is_array($rules)) {
            throw new \Exception('The rules method must return an array of rules');
        }

        return $rules;
    }

    private function getRulesFromRuleAttributesOnPublicProperties(): array
    {
        if (! $this->mapValidatedDataToPublicProperties) {
            return [];
        }

        return []; // TODO
    }

    public function rules(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Message Features
    |--------------------------------------------------------------------------
    */
    public function getAllValidationMessages(): array
    {
        return array_merge(
            $this->getMessagesFromTheMessagesProperty(),
            $this->getMessagesFromTheMessagesMethod(),
        );
    }

    private function getMessagesFromTheMessagesProperty(): array
    {
        if (isset($this->messages) && is_array($this->messages)) {
            return $this->messages;
        }

        return [];
    }

    private function getMessagesFromTheMessagesMethod(): array
    {
        $messages = $this->app->call([$this, 'messages'], ['action' => $this]);

        if (! is_array($messages)) {
            throw new \Exception('The messages method must return an array of messages');
        }

        return $messages;
    }

    public function messages(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Attribute Features
    |--------------------------------------------------------------------------
    */
    public function getAllValidationAttributes(): array
    {
        return array_merge(
            $this->getAttributesFromTheAttributesProperty(),
            $this->getAttributesFromTheAttributesMethod(),
        );
    }

    private function getAttributesFromTheAttributesProperty(): array
    {
        if (isset($this->attributes) && is_array($this->attributes)) {
            return $this->attributes;
        }

        return [];
    }

    private function getAttributesFromTheAttributesMethod(): array
    {
        $attributes = $this->app->call([$this, 'attributes'], ['action' => $this]);

        if (! is_array($attributes)) {
            throw new \Exception('The attributes method must return an array of attributes');
        }

        return $attributes;
    }

    public function attributes(): array
    {
        return [];
    }

    public function validate(): static
    {
        if (! $this->isValidationRequired()) {

            $this->didValidationPass = true;

            return $this;
        }

        return $this;
    }

    public function addError(string $key, string $message): static
    {
        $this->validator->errors()->add($key, $message);

        return $this;
    }
}
