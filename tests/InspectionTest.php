<?php

use Statix\FormAction\Inspector;
use Statix\FormAction\Validation\Rule;

// test the inspector can be instantiated given an object
test('the inspector can be instantiated given an object', function () {
    $inspector = new Inspector(new class
    {
    });

    expect($inspector)->toBeInstanceOf(Inspector::class);
});

// the inspector can be instantiated using the static make method
test('the inspector can be instantiated using the static make method', function () {
    $inspector = Inspector::make(new class
    {
    });

    expect($inspector)->toBeInstanceOf(Inspector::class);
});

// test the inspector can get all public properties
test('the inspector can get all public properties', function () {
    $inspector = new Inspector(new class
    {
        public string $name = 'John Doe';

        public int $age = 30;
    });

    $properties = $inspector->getPublicProperties();

    expect($properties)->toHaveCount(2);
});

// test the inspector can filter public properties
test('the inspector can filter public properties', function () {
    $inspector = new Inspector(new class
    {
        public string $name = 'John Doe';

        public int $age = 30;
    });

    $properties = $inspector->getPublicPropertiesWhere(function (ReflectionProperty $property) {
        return $property->getName() === 'name';
    });

    expect($properties)->toHaveCount(1);
    expect($properties[0]->getName())->toBe('name');
});

// test the inspector can filter public properties with attributes
test('the inspector can filter public properties with attributes', function () {
    $inspector = new Inspector(new class
    {
        #[Rule('required')]
        public string $name = 'John Doe';

        public int $age = 30;
    });

    $properties = $inspector->getPublicPropertiesWithAttribute(Rule::class);

    expect($properties)->toHaveCount(1);
    expect($properties[0]->getName())->toBe('name');
});

// test the inspector can get the type hints of a property
test('the inspector can get the type hints of a property', function () {
    $inspector = new Inspector(new class
    {
        public string $name = 'John Doe';
    });

    $properties = $inspector->getPublicProperties();

    $typeHints = $inspector->getPropertyTypeHints($properties[0]);

    expect($typeHints)->toHaveCount(2);
    expect($typeHints)->toContain('string', 'required');
});

// the inspector can get the type hints of a property that allows null
test('the inspector can get the type hints of a property that allows null', function () {
    $inspector = new Inspector(new class
    {
        public ?string $name = 'John Doe';
    });

    $properties = $inspector->getPublicProperties();

    $typeHints = $inspector->getPropertyTypeHints($properties[0]);

    expect($typeHints)->toHaveCount(2);
    expect($typeHints[0])->toBe('nullable');
    expect($typeHints[1])->toBe('string');
});

// the inspector can give you the name of a property
test('the inspector can give you the name of a property', function () {
    $inspector = new Inspector(new class
    {
        public ?string $name = 'John Doe';
    });

    $properties = $inspector->getPublicProperties();

    $name = $inspector->getPropertyName($properties[0]);

    expect($name)->toBe('name');
});

// the inspector can check if a public property exists
test('the inspector can check if a public property exists', function () {
    $inspector = new Inspector(new class
    {
        public ?string $name = 'John Doe';
    });

    expect($inspector->hasPublicProperty('name'))->toBeTrue();
    expect($inspector->hasPublicProperty('age'))->toBeFalse();
});

// the inspector can check if a public property has a default value
test('the inspector can check if a public property has a default value', function () {
    $inspector = new Inspector(new class
    {
        public ?string $name = 'John Doe';
    });

    expect($inspector->doesPropertyHaveDefaultValue('name'))->toBeTrue();
    expect($inspector->doesPropertyHaveDefaultValue('age'))->toBeFalse();
});

// the inspector can get the default value of a public property
test('the inspector can get the default value of a public property', function () {
    $inspector = new Inspector(new class
    {
        public ?string $name = 'John Doe';
    });

    expect($inspector->getPropertyDefaultValue('name'))->toBe('John Doe');
});
