# Fynix

[![Packagist Version](https://img.shields.io/packagist/v/bishalshrestha/fynix.svg?style=flat-square)](https://packagist.org/packages/bishalshrestha/fynix)
[![Downloads](https://img.shields.io/packagist/dt/bishalshrestha/fynix.svg?style=flat-square)](https://packagist.org/packages/bishalshrestha/fynix)
[![CI](https://github.com/shrestha-bishal/fynix/actions/workflows/ci.yml/badge.svg)](https://github.com/shrestha-bishal/fynix/actions/workflows/ci.yml)
[![PHP Version](https://img.shields.io/packagist/php-v/bishalshrestha/fynix.svg?style=flat-square)](https://packagist.org/packages/bishalshrestha/fynix)
[![License](https://img.shields.io/packagist/l/bishalshrestha/fynix.svg?style=flat-square)](LICENSE)

Fynix is a framework-agnostic PHP validation library for typed values, DTOs, files, nested objects, and arrays of objects. It provides immutable fluent rules, a central object validation handler, and structured errors suitable for forms and APIs.

Fynix v3 requires PHP 8.1 or newer.

## Installation

```bash
composer require bishalshrestha/fynix
```

## Quick start

Define a DTO, register its rules, and validate it at the application boundary:

```php
<?php

use Fynix\RuleSet;
use Fynix\ValidationHandler;
use Fynix\ValidationRegistry;

final class User
{
    public string $firstName = '';
    public string $email = '';
}

ValidationRegistry::register(
    User::class,
    static fn (RuleSet $rules): array => [
        $rules->string('firstName')->min(2)->max(50),
        $rules->email('email')->max(254),
    ],
);

$user = new User();
$user->firstName = 'A';
$user->email = 'not-an-email';

$errors = ValidationHandler::validate($user);
```

Registered validation reads DTO fields as public properties. Use public readable
properties for fields referenced by `RuleSet`, `Rule::on()`, or `RuleBuilder`.
The `nameof()` helper verifies that a property exists, but it does not bypass
private/protected visibility or call getters.

The default result contains messages keyed by property. When the input is invalid, `$errors` contains entries such as:

```php
[
    'firstName' => 'First Name is too short. This field must be at least 2 characters.',
    'email' => 'Email must be a valid email address.',
]
```

## Core concepts

Fynix has a deliberately small public surface:

| API | Responsibility |
| --- | --- |
| `Rule` | Create standalone validators. |
| `Rule::on()` | Create class-scoped validators with property checks. |
| `Rule::for()` | Bind a validator to a concrete object for direct validation. |
| `RuleSet` | Create scoped rules inside a registry factory. |
| `ValidationRegistry` | Store reusable rules for DTO classes. |
| `ValidationHandler` | Validate complete objects, nested graphs, and batches. |
| `ValidationError` | Represent structured field errors. |

Rules are immutable. Every fluent configuration method returns a new validator instance.

## Rule construction

### Standalone values

Use `Rule` when the value is being validated independently:

```php
use Fynix\Rule;

$emailError = Rule::email('email')
    ->max(254)
    ->validate('not-an-email');

$passwordErrors = Rule::password('password')
    ->length(12, 128)
    ->validateAll('short');
```

`validate()` returns the first applicable `ValidationError` or `null`. `validateAll()` returns every applicable error as a list.

### Class-scoped rules

Use `Rule::on()` when defining rules for a DTO outside a registry:

```php
$rules = [
    Rule::on(User::class)->string('firstName')->min(2)->max(50),
    Rule::on(User::class)->email('email')->max(254),
];

$errors = ValidationHandler::validate($user, rules: $rules);
```

The owning class and property must exist. Invalid definitions throw typed rule-definition exceptions.

### Object-bound validation

Use `Rule::for()` for a direct check against one object instance:

```php
$error = Rule::for($user)
    ->string('firstName')
    ->min(2)
    ->max(50)
    ->validate();
```

This is useful for a focused field check without registering a complete object rule set. Nested object validators are handled by `ValidationHandler`.

## Requiredness and conditions

Fields are required by default. Use `optional()` to allow `null` or an empty value, or use `required(false)` when the boolean form is clearer in a shared rule factory:

```php
ValidationRegistry::register(
    Registration::class,
    static fn (RuleSet $rules): array => [
        $rules->string('companyName')
            ->optional()
            ->requiredIf('accountType', 'business'),
        $rules->string('nickname')
            ->optional()
            ->prohibitedIf('accountType', 'business'),
    ],
);
```

Available conditional methods are `requiredIf()`, `requiredUnless()`, `prohibitedIf()`, and `prohibitedUnless()`. Use `sameAs()` and `differentFrom()` for related fields.

## Nested objects

Register each nested DTO, then connect it from the parent rule set:

```php
final class Address
{
    public string $city = '';
}

final class Customer
{
    public ?Address $address = null;
}

ValidationRegistry::register(
    Address::class,
    static fn (RuleSet $rules): array => [
        $rules->string('city')->min(2)->max(80),
    ],
);

ValidationRegistry::register(
    Customer::class,
    static fn (RuleSet $rules): array => [
        $rules->object('address', Address::class)->optional(),
    ],
);

$errors = ValidationHandler::validate(new Customer());
```

Use `objectArray()` for a collection of nested DTOs:

```php
ValidationRegistry::register(
    Order::class,
    static fn (RuleSet $rules): array => [
        $rules->objectArray('items', LineItem::class)->min(1)->max(100),
    ],
);
```

Fynix validates the parent structure, checks each nested object type, and resolves the nested class rules through the registry.

## Error output

### String messages

String messages are the default and are convenient for server-rendered forms:

```php
$errors = ValidationHandler::validate($user);
$flatErrors = ValidationHandler::validateAndFlatten($user);

// firstName => "First Name is required."
```

For nested values, flattened keys use dot notation such as `address.city` or `items.0.name`.

### Structured errors

Set `flattenErrorToString` to `false` when an API needs stable codes and parameters:

```php
$errors = ValidationHandler::validate(
    $user,
    flattenErrorToString: false,
);

$payload = [];
foreach ($errors as $field => $issues) {
    foreach ((array) $issues as $issue) {
        $payload[] = $issue->toArray();
    }
}
```

Each `ValidationError` exposes `field_name`, `rule`, `message`, `code`, `parameters`, and `toArray()`.

## Built-in validators

| Rule method | Use case |
| --- | --- |
| `string()` | Text values, names, labels, and free-form strings. |
| `boolean()` | Strict boolean values. |
| `number()` | Numeric values and ranges. |
| `integer()` | Strict integer values. |
| `decimal()` | Strict decimal values. |
| `email()` | Email address format and optional domain checks. |
| `phoneNumber()` | Phone number format and length. |
| `password()` | Password length and strength. |
| `dateTime()` | Date, time, and `DateTimeInterface` values. |
| `url()` | URL values. |
| `uuid()` | UUID values. |
| `ipAddress()` | IPv4 and IPv6 addresses. |
| `regex()` | Custom regular-expression formats. |
| `array()` / `arrayOf()` | Arrays and repeated scalar rules. |
| `enum()` | Backed enum values or enum instances. |
| `file()` | Uploaded files. |
| `image()` / `images()` | One or many uploaded images. |
| `object()` | One nested DTO. |
| `objectArray()` | An array of nested DTOs. |
| `username()` | Username format and application-provided uniqueness checks. |

All validators share common methods such as `required()`, `optional()`, `min()`, `max()`, `length()`, `in()`, `notIn()`, and conditional constraints where supported.

## Batch validation

Validate several registered objects in one call:

```php
$results = ValidationHandler::validateMany($user, $profile);

$namedResults = ValidationHandler::validateManyAssoc([
    'user' => $user,
    'profile' => $profile,
]);
```

For associative validation, pass `flattenErrorToString: false` to `validateManyAssoc()` when structured errors are required.

## Composable rules

Use `AllOf`, `AnyOf`, and `Not` for reusable business constraints:

```php
use Fynix\Rule;
use Fynix\Rules\AllOf;
use Fynix\Rules\AnyOf;
use Fynix\Rules\Not;

$name = new AllOf([
    Rule::string('name')->min(2),
    Rule::string('name')->max(80),
]);

$contact = new AnyOf([
    Rule::email('contact'),
    Rule::phoneNumber('contact'),
]);

$status = new Not(
    Rule::string('status')->in(['blocked']),
    'This status is not allowed.',
);
```

## Extending Fynix

Create a custom validator by extending `ValidatorBase` and implementing the type-specific check:

```php
use Fynix\ValidationError;
use Fynix\Validators\ValidatorBase;

final class EvenNumberValidator extends ValidatorBase
{
    protected function validateValue(mixed $fieldValue): ?ValidationError
    {
        if (!is_int($fieldValue) || $fieldValue % 2 !== 0) {
            return new ValidationError($this, "$this->name must be even.", 'number.even');
        }

        return null;
    }
}
```

The base pipeline handles shared requiredness and common constraints before the custom type check runs.

## Validation listeners

Implement `ValidationListener` for logging, metrics, or tracing around object validation:

```php
ValidationHandler::addListener($listener);
$errors = ValidationHandler::validate($user);
ValidationHandler::clearListeners();
```

Listeners receive callbacks before and after validation.

## Testing

Run the package test suite and static analysis with Composer:

```bash
composer test
composer analyse
```

The repository also provides a Docker-based test environment:

```bash
docker compose run --build --rm test
```

## Documentation

The full documentation site contains the complete validator reference, method reference, advanced patterns, and a production-style registration walkthrough:

https://fynixphp.netlify.app

## Contributing

Issues and pull requests are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before submitting a change.

## License

Fynix is released under the [MIT License](LICENSE).

## Links

- [Repository](https://github.com/shrestha-bishal/fynix)
- [Packagist](https://packagist.org/packages/bishalshrestha/fynix)
- [Documentation](https://fynixphp.netlify.app)
