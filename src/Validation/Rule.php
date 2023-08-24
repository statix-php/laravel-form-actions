<?php

namespace Statix\FormAction\Validation;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class Rule
{
    public function __construct(
        public $rule,
        protected $attribute = null,
        protected $as = null,
        protected $message = null,
    ) {
        //
    }

    public function getRules(): array
    {
        // if the rule is already an array, we'll assume it's a list of rules and return it as is
        if (is_array($this->rule)) {
            return $this->rule;
        }

        // if the rule is a string, we'll assume it's a single rule and return it as is
        if (is_string($this->rule)) {
            return [$this->rule];
        }

        // if the rule is an object, we'll assume it's a rule object and return it as is
        return [$this->rule];
    }
}
