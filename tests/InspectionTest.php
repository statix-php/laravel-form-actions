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

// test the inspection can get all public properties
test('the inspection can get all public properties', function () {
    $inspector = new Inspector(new class
    {
        public string $name = 'John Doe';

        public int $age = 30;
    });

    $properties = $inspector->findPublicProperties();

    expect($properties)->toHaveCount(2);
});

// test the inspector can filter public properties
test('the inspector can filter public properties', function () {
    $inspector = new Inspector(new class
    {
        public string $name = 'John Doe';

        public int $age = 30;
    });

    $properties = $inspector->findPublicPropertiesWhere(function (ReflectionProperty $property) {
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

    $properties = $inspector->findPublicPropertiesWithAttribute(Rule::class);

    expect($properties)->toHaveCount(1);
    expect($properties[0]->getName())->toBe('name');
});

// test the inspection can get the type hints of a property
test('the inspection can get the type hints of a property', function () {
    $inspector = new Inspector(new class
    {
        public string $name = 'John Doe';
    });

    $properties = $inspector->findPublicProperties();

    $typeHints = $inspector->getPropertyTypeHints($properties[0]);

    expect($typeHints)->toHaveCount(1);
    expect($typeHints[0])->toBe('string');
});

// the inspection can get the type hints of a property that allows null
test('the inspection can get the type hints of a property that allows null', function () {
    $inspector = new Inspector(new class
    {
        public ?string $name = 'John Doe';
    });

    $properties = $inspector->findPublicProperties();

    $typeHints = $inspector->getPropertyTypeHints($properties[0]);

    expect($typeHints)->toHaveCount(2);
    expect($typeHints[0])->toBe('nullable');
    expect($typeHints[1])->toBe('string');
});

// the inspection can give you the name of a property
test('the inspection can give you the name of a property', function () {
    $inspector = new Inspector(new class
    {
        public ?string $name = 'John Doe';
    });

    $properties = $inspector->findPublicProperties();

    $name = $inspector->getPropertyName($properties[0]);

    expect($name)->toBe('name');
});
