<?php

namespace Statix\FormAction\Validation;

abstract class BaseRule
{
}

/**
 * How does this work in general
 *
 * 1. The getAllValidationRules() method in the FormAction class will get all the rules from the class
 * 2. If public property has a #[Rule] attribute, then we will get the rules from the attribute, and add it to the rules array. The key will be the property name, and the value will be the rules
 * 3. The validation will occur in the FormAction class
 */
