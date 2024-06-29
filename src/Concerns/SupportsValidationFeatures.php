<?php

namespace Statix\FormAction\Concerns;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use ReflectionProperty;
use Statix\FormAction\FormAction;
use Statix\FormAction\Inspector;
use Statix\FormAction\Validation\Rules;

trait SupportsValidationFeatures
{
    protected Validator $validator;

    protected function getValidatorInstance(): Validator
    {
        /** @var FormAction $this */
        if (isset($this->validator)) {
            return $this->validator;
        }

        if (method_exists($this, 'validator')) {
            $validator = $this->app->call([$this, 'validator']);

            if (! $validator instanceof Validator) {
                throw new \Exception('The validator method must return an instance of '.Validator::class);
            }

            $this->validator = $validator;
        } else {
            /** @var ValidationFactory $factory */
            $factory = $this->app->make(ValidationFactory::class);

            /** @var Validator $validator */
            $validator = $factory->make(
                $this->request->all(),
                $this->getAllValidationRules(),
                $this->getAllValidationMessages(),
                $this->getAllValidationAttributes()
            );

            $validator->stopOnFirstFailure($this->shouldStopOnFirstFailure);

            $this->validator = $validator;
        }

        return $this->validator;
    }

    protected bool $didValidationPass = false;

    public function validated(string|array|int|null $key = null, mixed $default = null): mixed
    {
        /** @var FormAction $this */
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

    public function failedValidation()
    {
        if (empty($this->onFailedValidationCallbacks)) {
            throw new ValidationException($this->getValidatorInstance());
        }

        $this->runOnFailedValidationCallbacks();
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rule Features
    |--------------------------------------------------------------------------
    */
    protected $rulesFromAttributes = [];

    public function getAllValidationRules(): array
    {
        $rules = array_merge_recursive(
            $this->getRulesFromTheRulesProperty(),
            $this->getRulesFromTheRulesMethod(),
            $this->getRulesFromRuleAttributesOnPublicProperties(),
        );

        foreach ($rules as $key => $value) {
            if (is_array($value)) {
                $rules[$key] = array_values(array_unique($value, SORT_REGULAR));
            } else {
                $rules[$key] = $value;
            }
        }

        return $rules;
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

        // need to check if the values are pipe delimited rules
        foreach ($rules as $key => $value) {
            if (is_string($value) && strpos($value, '|') !== false) {
                $rules[$key] = explode('|', $value);
            }
        }

        return $rules;
    }

    private function getRulesFromRuleAttributesOnPublicProperties(): array
    {
        $rules = [];

        $inspector = Inspector::make($this);

        $properties = $inspector->getPublicPropertiesWithAttribute(Rules::class);

        foreach ($properties as $property) {

            /** @var ReflectionProperty $property */
            $name = $inspector->getPropertyName($property);

            if ($inspector->propertyHasTypehints($property)) {
                $types = $inspector->getPropertyTypeHints($property);
            }

            $attributes = $inspector->getPropertyAttributes($property, Rules::class);

            foreach ($attributes as $attribute) {
                /** @var ReflectionAttribute $attribute */
                $attRules = Arr::flatten($attribute->newInstance()->getRules());

                // check if the types already has any of the rules
                foreach ($attRules as $attRule) {
                    if (in_array($attRule, $types)) {
                        continue;
                    }

                    $types[] = $attRule;
                }
            }

            $types = Arr::flatten($types);

            $rules[$name] = array_values(Arr::flatten($types));
        }

        return $rules;
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

    public function addError(string $key, string $message): static
    {
        $this->validator->errors()->add($key, $message);

        return $this;
    }

    public function validate(): static
    {
        if (! $this->isValidationRequired()) {

            $this->didValidationPass = true;

            return $this;
        }

        $this->runBeforeValidationCallbacks();

        $validator = $this->getValidatorInstance();

        if ($validator->fails()) {
            $this->didValidationPass = false;

            $this->failedValidation();
        } else {
            $this->didValidationPass = true;

            if ($this->mapValidatedDataToPublicProperties) {
                $this->attemptToMapValidatedDataToPublicProperties();
            }

            $this->runAfterValidationCallbacks();
        }

        return $this;
    }
}
