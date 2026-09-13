# Fynix - The Modern PHP Validation Engine <img width="30" height="30" alt="logo" src="https://github.com/user-attachments/assets/e231643d-88f5-428b-a34a-43a906d2cb9c" />

[![Packagist Version](https://img.shields.io/packagist/v/bishalshrestha/fynix.svg?style=flat-square)](https://packagist.org/packages/bishalshrestha/fynix)
[![Downloads](https://img.shields.io/packagist/dt/bishalshrestha/fynix.svg?style=flat-square)](https://packagist.org/packages/bishalshrestha/fynix)
![CI](https://github.com/shrestha-bishal/fynix/actions/workflows/ci.yml/badge.svg)
![PHP Version](https://img.shields.io/packagist/php-v/bishalshrestha/fynix.svg?style=flat-square)
![License](https://img.shields.io/packagist/l/bishalshrestha/fynix.svg?style=flat-square)

**fynix** is a modern, extensible PHP library for validating primitive data types, files, complex objects, and nested objects, including arrays of objects. Designed for high performance and flexibility, it enables developers to implement robust validation logic across modern PHP applications and frameworks.

<img width="1024" height="1024" alt="logo" src="https://github.com/user-attachments/assets/d498f52c-52db-4543-aafd-68318bc7df34" />


# Table of Contents

1. [Overview Example](#overview-example)
    - [FreightDto](#freightdto)
    - [Nested DTO Structure](#nested-dto-structure)
    - [Setting Up Example Validation](#setting-up-example-validation)
    - [Validating DTOs](#validating-dtos)
    - [Validation Example Image](#validation-example-image)
    - [Flattened Validation Example Image](#flattened-validation-example-image)

2. [Features](#features)

3. [Architecture Overview](#architecture-overview)
    - Validator Classes
    - Fluent Validator Configuration
    - ValidationHandler
    - ValidationRegistry
    - ValidationError

4. [Validator Classes](#validator-classes)
    - ValidatorBase
    - StringValidator
    - BooleanValidator
    - NumberValidator
    - IntegerValidator
    - DecimalValidator
    - DateTimeValidator
    - EmailValidator
    - UrlValidator
    - UuidValidator
    - IpAddressValidator
    - RegexValidator
    - ArrayValidator
    - EnumValidator
    - FileValidator
    - PhoneNumberValidator
    - PasswordValidator
    - ImageValidator
    - ImagesValidator
    - ObjectValidator
    - ObjectArrayValidator
    - UsernameValidator

5. [Fluent Validator Configuration](#fluent-validator-configuration)
    - [Structured Errors](#structured-errors)

6. [Validation Matrix](#validation-matrix)
    - Feature Comparison Table
    - Key Capabilities
        - Nullability
        - Min/Max Length
        - Min/Max Number
        - HTML Exclusion
        - Format/Pattern
        - Uniqueness/Existence
        - Nested Validation
        - Array Validation

7. [Basic Usage Examples](#basic-usage-examples)
    - [Property Names](#property-names)
    - [RuleSet Registry Definitions](#ruleset-registry-definitions)
    - [Rule Facade](#rule-facade)
    - [Direct Validation Without a Registry](#direct-validation-without-a-registry)
    - [String Validation](#string-validation)
    - [Email Validation](#email-validation)
    - [Number Validation](#number-validation)
    - [Password Validation](#password-validation)
    - [Image Validation](#image-validation)
    - [Nested Object Validation](#nested-object-validation)
    - [Array of Objects Validation](#array-of-objects-validation)
    - [Example: Full User Registration Validation](#example-full-user-registration-validation)

8. [Advanced Usage](#advanced-usage)
    - [Batch Validation](#batch-validation)
    - [Associative Validation](#associative-validation)
    - [Error Normalization](#error-normalization)
    - [Registering Custom Validation Rules](#registering-custom-validation-rules)
    - [Reusable Validation Rules](#reusable-validation-rules)

9. [Core Classes and Their Roles](#core-classes-and-their-roles)
    - [Validator](#validator)
    - [ValidationHandler](#validationhandler)
    - [ValidationRegistry](#validationregistry)
    - [ValidationError](#validationerror)


10. [Extending the Library](#extending-the-library)
    - Creating Custom Validators

11. [Best Practices & Advanced Patterns](#best-practices--advanced-patterns)
    - Centralize Validation Logic
    - Normalize Errors for UI
    - Custom Validators
    - Fluent Configuration
    - Batch Validation

12. [Installation](#installation)

13. [Migration from v1 and v2](#migration-from-v1-and-v2)

14. [Migration to v3](#migration-to-v3)

15. [Testing](#testing)

16. [Contributing](#contributing)
    - Forking & Branching
    - Committing
    - Running Tests
    - Pull Requests
    - Reporting Issues

17. [Funding & Sponsorship](#funding--sponsorship)
    - [Support Options](#support-options)
      - GitHub Sponsors
      - Buy Me a Coffee
      - Thanks.dev

17. [License](#license)

18. [Author](#author)
    - GitHub Profile
    - Repository
    - Website
    - Packagist Link

## Overview Example
### FreightDto
```php
<?php 
namespace App\Dto\Quote;;

use App\Dto\Address\AddressDto;
use App\Traits\ArrayConvertible;
use DateTime;

class FreightDto {
    use ArrayConvertible;

    public ?int $id = null;

    /**@var PackageDto[] */
    public array $packages = [];
    public ?string $customerName;
    public ?DateTime $shippingDate;
    public bool $containsDangerousGoods = false;
    public AddressDto $pickupAddress;
    public AddressDto $deliveryAddress;
}
```

### Nested DTO Structure
```
$freight = new FreightDto();
$freight->packages[] = (new PackageDto())->items[] = new ItemDto();
$freight->pickupAddress = new AddressDto();
$freight->deliveryAddress = new AddressDto();
```

### Setting Up Example Validation
Validation rules are registered using the `ValidationRegistry`. You can organize rules by DTO type for better structure and maintainability.
```php
<?php
class ValidationRuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        self::registerDimensionValidation();
        self::registerItemValidation();
        self::registerAddressValidation();
        self::registerShippingValidation();
        self::registerPackageValidation();
    }

    private static function registerDimensionValidation(): void {
        ValidationRegistry::register(DimensionDto::class, static function (RuleSet $rules): array {
            return [
                $rules->number('lengthCm')->min(1)->max(1800),
                $rules->number('widthCm')->min(1)->max(1800),
                $rules->number('heightCm')->min(1)->max(2000),
                $rules->number('weightKg')->min(1)->max(1000)
            ];
        });
    }

    private static function registerItemValidation() : void
    {
       ValidationRegistry::register(ItemDto::class, static function (RuleSet $rules): array {
            return [
                $rules->string('description'),
                $rules->object('dimension', DimensionDto::class),
           ];
       });
    }

    private static function registerAddressValidation() : void 
    {
        ValidationRegistry::register(AddressDto::class, static function (RuleSet $rules): array {
            return [
            $rules->string('suburb'),
            $rules->number('postcode')->length(2, 10),
            $rules->string('state')->length(2, 6),
            $rules->string('countryCode')->length(2, 4)
            ];
        });
    }

    private static function registerShippingValidation(): void 
    {
        ValidationRegistry::register(FreightDto::class, static function (RuleSet $rules): array {
            return[
            $rules->objectArray('packages', PackageDto::class),
            $rules->string('customerName')->length(0, 50)->optional(),
            $rules->object('pickupAddress', AddressDto::class),
            $rules->object('deliveryAddress', AddressDto::class),
            ];
        });
    }

    private static function registerPackageValidation(): void {
        ValidationRegistry::register(PackageDto::class, static function (RuleSet $rules): array {
            return [
            $rules->string('type'),
            $rules->string('description')->length(0, 50)->optional(),
            $rules->object('dimensions', DimensionDto::class),
            $rules->objectArray('items', ItemDto::class)
            ];
        });
    }
}
```

### Validating DTOs
Once your validation rules are registered, you can validate DTO instances anywhere in your application:
```php
<?php
    $dto = DtoMapper::toFreightDto($args);
    $errors = ValidationHandler::validate($dto);
    $flattenedErrors = ValidationHandler::flattenValidationErrors($errors);
    
    if(count($errors) > 0) 
        return;
```

![Validation Example Image](https://github.com/user-attachments/assets/e2454460-9936-4aa9-877a-fbff3d7267a9)
![Flattened Validation Example Image](https://github.com/user-attachments/assets/941e4f32-ad6f-4817-9477-e2e1870a4257)
---

## Features
- **Comprehensive Validation**: Strings, numbers, booleans, dates, emails, phone numbers, passwords, URLs, UUIDs, enums, files, images, arrays, nested objects, and arrays of objects.
- **Extensible Architecture**: Easily add custom validation rules or extend built-in validators.
- **Fluent Configuration**: Fine-grained control over required fields, length, numeric ranges, file types, and more.
- **Nested & Array Validation**: Validate nested objects and arrays of objects using registered rules.
- **Error Normalization**: Flatten nested error structures for easy form binding.
- **Centralized Registry**: Register and retrieve validation rules for any class.
- **Open Source**: MIT-licensed and open for contributions.

--- 

## Architecture Overview

The library is organized into several core components:
- **Validator Classes**: Each validator encapsulates logic for a specific data type or structure.
- **Fluent Configuration**: Validators configure their supported constraints through chainable methods such as `min()`, `max()`, `length()`, and `optional()`.
- **ValidationHandler**: Orchestrates validation, supports batch and associative validation, and error normalization.
- **ValidationRegistry**: Central registry for registering and retrieving validation rules for custom classes.
- **ValidationError**: Standardized error object with a field, machine-readable code, message, and parameters.

## Validator Classes

| Validator                | Description                                                                                   | Fluent configuration                                                                       |
|--------------------------|-----------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------|
| StringValidator          | Validates string type, length, nullability, and excludes HTML tags.                           | `length()`, `min()`, `max()`, `optional()`                                                  |
| BooleanValidator         | Validates strict boolean values.                                                              | `optional()`                                                                                |
| NumberValidator          | Validates numeric type and enforces min/max value constraints.                                | `min()`, `max()`, `length()`, `optional()`                                                   |
| IntegerValidator         | Validates strict integer values and min/max constraints.                                      | `min()`, `max()`, `optional()`                                                               |
| DecimalValidator         | Validates strict floating-point values and min/max constraints.                               | `min()`, `max()`, `optional()`                                                               |
| DateTimeValidator        | Validates date strings, date-time strings, and DateTimeInterface values.                     | `format()`, `optional()`                                                                    |
| EmailValidator           | Validates email format, optional DNS, and structure.                                          | `length()`, `verifyDomain()`, `optional()`                                                   |
| UrlValidator             | Validates URL values.                                                                         | `length()`, `optional()`                                                                    |
| UuidValidator            | Validates UUID values.                                                                        | `optional()`                                                                                |
| IpAddressValidator       | Validates IPv4 and IPv6 addresses.                                                            | `optional()`                                                                                |
| RegexValidator           | Validates strings against a regular expression.                                               | `optional()`                                                                                |
| ArrayValidator           | Validates arrays, item counts, and optional per-item rules.                                  | `min()`, `max()`, `each()`, `optional()`                                                     |
| EnumValidator            | Validates backed enum values or enum instances.                                               | `optional()`                                                                                |
| FileValidator            | Validates uploaded files, size, and optional extensions.                                      | `maxFileSizeMB()`, `extensions()`, `optional()`                                              |
| UsernameValidator        | Validates username format and optional application-backed uniqueness.                         | `length()`, `uniqueUsing()`, `optional()`                                                    |
| PhoneNumberValidator     | Validates phone number format, allowed symbols, and length.                                   | `length()`, `optional()`                                                                    |
| PasswordValidator        | Enforces password strength: uppercase, lowercase, number, special character, length.          | `length()`, `optional()`                                                                    |
| ImageValidator           | Validates a single image file: size, extension, and actual image content.                     | `maxFileSizeMB()`, `optional()`                                                             |
| ImagesValidator          | Validates an array of image files, each using ImageValidator.                                 | `min()`, `max()`, `optional()`                                                              |
| ObjectValidator          | Validates a nested object property using registered rules for its class.                      | `required()`, `optional()`                                                                   |
| ObjectArrayValidator     | Validates an array of objects, each using registered rules for its class.                     | `min()`, `max()`, `required()`, `optional()`                                                  |

### ValidatorBase
Abstract base for all validators. Implements the shared validation pipeline (requiredness, normalization, length, HTML exclusion, allowed/disallowed values, and cross-field constraints) and requires child classes to implement protected `validateValue($fieldValue)` for type-specific logic.

Generic constraints include `in()`, `notIn()`, `sameAs()`, `differentFrom()`, `requiredIf()`, `requiredUnless()`, `prohibitedIf()`, and `prohibitedUnless()`. Cross-field constraints are evaluated when validating a registered object:
```php
ValidationRegistry::register(RegistrationDto::class, static fn (RuleSet $rules): array => [
    $rules->string('passwordConfirmation')->sameAs('password'),
    $rules->string('companyName')->optional()->requiredIf('accountType', 'business'),
]);
```

For one-off validation of an object property, bind the rule directly to the object:
```php
$error = Rule::for($user)
    ->string('name')
    ->min(2)
    ->max(30)
    ->validate();
```

### StringValidator
Validates string type, length, nullability, and excludes HTML tags. Usage:
```php
$validator = Rule::string('firstName')->length(2, 50);
```

### NumberValidator
Validates numeric type and enforces min/max value constraints. Usage:
```php
$validator = Rule::number('age')->min(18)->max(99);
```

### EmailValidator
Validates email format, optional DNS, and structure. Usage:
```php
$validator = Rule::email('email')->verifyDomain();
```

### UsernameValidator
Usernames can use an application-provided database or repository callback for uniqueness:
```php
$validator = Rule::username('username')
    ->uniqueUsing(fn (string $username): bool => $userRepository->existsByUsername($username));
```

### PhoneNumberValidator
Validates phone number format, allowed symbols, and length. Usage:
```php
$validator = Rule::phoneNumber('phoneNumber')->length(10, 12);
```

### PasswordValidator
Enforces password strength: uppercase, lowercase, number, special character, length. Usage:
```php
$validator = Rule::password('password')->length(8, 30);
```

### ImageValidator
Validates a single image file: size, extension, and actual image content. Usage:
```php
$validator = Rule::image('profilePic')->maxFileSizeMB(5);
```

### ImagesValidator
Validates an array of image files, each using ImageValidator. Usage:
```php
$validator = Rule::images('galleryImages')->min(1)->max(5);
```

### ObjectValidator
Validates a nested object property using registered rules for its class. Usage:
```php
$validator = Rule::object('address', UserAddress::class);
```

### ObjectArrayValidator
Validates an array of objects, each using registered rules for its class. Usage:
```php
$validator = Rule::objectArray('items', FreightItemDto::class);
```

## Fluent Validator Configuration

Validators are configured through immutable fluent methods. Each method returns a new validator instance. `min()` and `max()` set length for string-like validators, numeric bounds for `NumberValidator`, and image count for `ImagesValidator`. Use `maxFileSizeMB()` for image files. Use `required()` and `optional()` for unconditional requiredness, and `requiredIf()` or `requiredUnless()` for conditions based on another property. Email domain checks are opt-in through `verifyDomain()` so validation does not require network access by default.

### Structured Errors

`ValidationHandler::validate()` returns messages by default. Pass `flattenErrorToString: false` to receive `ValidationError` objects:

```php
$errors = ValidationHandler::validate($user, flattenErrorToString: false);

foreach ($errors as $field => $error) {
    foreach ((array) $error as $issue) {
        echo $issue->code;
        echo $issue->message;
    }
}
```

Each error also exposes `field_name`, `rule`, `parameters`, and `toArray()` for API responses.

For all applicable errors on a single field, use `validateFieldAll()`:

```php
$errors = Rule::password('password')
    ->validateFieldAll('abc');
```

`validateField()` remains available when an application only needs the first error.

## Validation Matrix

| Feature                  | String | Number | Email | Phone | Password | Image | Images | Object | ObjectArray |
|--------------------------|--------|--------|-------|-------|----------|-------|--------|--------|------------|
| Nullability              | ✓      | ✓      | ✓     | ✓     | ✓        | ✓     | ✓      | ✓      | ✓          |
| Min/Max Length           | ✓      | ✓      | ✓     | ✓     | ✓        |       |        |        |            |
| Min/Max Number           |        | ✓      |       |       |          |       |        |        |            |
| HTML Exclusion           | ✓      |        | ✓     |       |          |       |        |        |            |
| Format/Pattern           |        |        | ✓     | ✓     | ✓        | ✓     | ✓      |        |            |
| Uniqueness/Existence     |        |        | ✓     |       | ✓        |       |        |        |            |
| Nested Validation        |        |        |       |       |          |       |        | ✓      | ✓          |
| Array Validation         |        |        |       |       |          |       | ✓      |        | ✓          |

## Basic Usage Examples

### Property Names
Use `Rule::on()` or `RuleSet` to validate property names against the owning class when defining rules:
```php
use Fynix\Rule;

$validator = Rule::on(User::class)->string('firstName');
```

Misspelled properties throw a typed rule-definition exception when the rule is created.

### RuleSet Registry Definitions
For DTO rules, use a static `ValidationRegistry` closure receiving a `RuleSet`. `RuleSet` delegates to `Rule::on()` and checks each property against the owning class.

```php
use Fynix\RuleSet;
use Fynix\ValidationRegistry;

class User
{
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public int $age = 0;
    public string $password = '';
}

ValidationRegistry::register(
    User::class,
    static fn (RuleSet $rules): array => [
        $rules->string('firstName')->min(2)->max(50),
        $rules->string('lastName')->min(2)->max(50),
        $rules->email('email')->max(255),
        $rules->number('age')->min(18)->max(120),
        $rules->password('password')->min(8)->max(128),
    ]
);
```

Registry definitions remain static in v3. The old `Rules::for()` and `RuleBuilder` APIs were removed.

If you only want the generated rules array without registering it immediately, use `Rule::on()` directly:

```php
$rules = [
    Rule::on(User::class)->string('firstName')->min(2)->max(50),
    Rule::on(User::class)->string('lastName')->min(2)->max(50),
];
```

This is the rule definition stage; actual validation still happens when you call `ValidationHandler::validate($user)` or use a validator directly.

### Rule Facade
`Rule` is the v3 entry point for standalone validators. `Rule::on()` is its class-scoped rule-definition counterpart, `Rule::for()` binds a rule to an object for direct validation, and `RuleSet` is the registry-definition facade. Validator constructors are protected in v3; `Rule` and `ScopedRule` are the supported construction paths.

```php
use Fynix\Rule;

$bare = Rule::string('firstName');
$checked = Rule::on(User::class)->string('firstName');
```

The bare form derives its label automatically. The class-scoped form validates the owner class and property using the `nameof()` helper, throwing typed rule-definition exceptions. The object-bound form reads the property from the supplied instance when `validate()` is called. Nested object validators remain handler-only and throw a `LogicException` if used directly through `Rule::for()`.

### Direct Validation Without a Registry
When you do not need object-level rule registration, validate each value directly with a validator instance. `validate()` runs the complete shared pipeline; `validateField()` remains available when you want to make the value explicit or provide an owning object for cross-field rules. `ValidationHandler::validate()` is for registered objects and cannot infer rules for an unregistered `User` class. This approach is useful for form fields, ad hoc checks, and isolated DTO members.

```php
use Fynix\Rule;
use Fynix\ValidationError;

final class User
{
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public int $age = 0;
    public string $password = '';
}

$user = new User();
$user->firstName = 'John';
$user->lastName = 'Doe';
$user->email = 'john@example.com';
$user->age = 32;
$user->password = 'StrongPassword123!';

// Validate all five properties without registering User::class.
$errors = array_filter([
    'firstName' => Rule::string('firstName')->length(2, 50)->validate($user->firstName),
    'lastName' => Rule::string('lastName')->length(2, 50)->validate($user->lastName),
    'email' => Rule::email('email')->validate($user->email),
    'age' => Rule::number('age')->min(18)->max(120)->validate($user->age),
    'password' => Rule::password('password')->length(8, 128)->validate($user->password),
]);

// Single-field validation returning the first error
$error = Rule::string('firstName')
    ->min(2)
    ->max(50)
    ->validateField('J');

// Validate all applicable errors for a field
$allErrors = Rule::email('email')
    ->validateFieldAll('not-an-email');

// Numeric range validation
$ageErrors = Rule::number('age')
    ->min(18)
    ->max(99)
    ->validateFieldAll(16);

if ($error instanceof ValidationError) {
    echo $error->message;
}
```

This approach is ideal when the validation rules are local to a form or request payload and do not need to be reused via `ValidationRegistry`.

### String Validation
```php
$stringValidator = Rule::string('firstName')->length(2, 50);
```

### Email Validation
```php
$emailValidator = Rule::email('email');
```

### Number Validation
```php
$numberValidator = Rule::number('age')->min(18)->max(99);
```

### Password Validation
```php
$passwordValidator = Rule::password('password')->length(8, 30);
```

### Image Validation
```php
$imageValidator = Rule::image('profilePic')->maxFileSizeMB(5);
```

### Nested Object Validation
```php
$objectValidator = Rule::object('address', UserAddress::class);
```

### Array of Objects Validation
```php
$objectArrayValidator = Rule::objectArray('items', FreightItemDto::class);
```

### Example: Full User Registration Validation
Below is a practical example showing how to use the v3 rule registry for a user registration form with multiple fields and nested validation:

```php
use Fynix\RuleSet;
use Fynix\ValidationRegistry;
use Fynix\ValidationHandler;

class UserAddress {
    public ?string $street = null;
    public ?string $city = null;
    public ?string $postcode = null;
}

class User {
    public ?string $firstName = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?UserAddress $address = null;

}

ValidationRegistry::register(UserAddress::class, static function (RuleSet $rules): array {
    return [
        $rules->string('street'),
        $rules->string('city'),
        $rules->string('postcode'),
    ];
});

ValidationRegistry::register(User::class, static function (RuleSet $rules): array {
    return [
        $rules->string('firstName')->min(2),
        $rules->email('email'),
        $rules->password('password')->min(8),
        $rules->object('address', UserAddress::class),
    ];
});

$user = new User();
$user->firstName = 'John';
$user->email = 'john@example.com';
$user->password = 'Password123!';
$user->address = new UserAddress();
$user->address->street = '123 Main St';
$user->address->city = 'Metropolis';
$user->address->postcode = '12345';

$errors = ValidationHandler::validate($user);
if (!empty($errors)) {
    // Handle errors
}
```
---

## Advanced Usage
### Batch Validation
```php
use Fynix\ValidationHandler;

$errors = ValidationHandler::validateMany($user, $profile, $settings);
```

### Associative Validation
```php
$instances = ['user' => $user, 'profile' => $profile];
$errors = ValidationHandler::validateManyAssoc($instances);
```

### Error Normalization
```php
$errors = ValidationHandler::validate($user);
$flatErrors = ValidationHandler::flattenValidationErrors($errors);
```
![Validation Example](https://github.com/user-attachments/assets/e2454460-9936-4aa9-877a-fbff3d7267a9)
![Flattened Validation Example](https://github.com/user-attachments/assets/941e4f32-ad6f-4817-9477-e2e1870a4257)

### Registering Custom Validation Rules
```php
use Fynix\RuleSet;
use Fynix\ValidationRegistry;

ValidationRegistry::register(User::class, static function (RuleSet $rules): array {
    return [
        $rules->string('firstName'),
        $rules->email('email'),
        // ... other rules
    ];
});
```

### Reusable Validation Rules
`fynix` allows you to define reusable validation rules for your **data transfer objects (DTOs)** using the `ValidationRegistry`.
Each registry factory receives a `RuleSet`, so rule definitions are type-safe and reusable.

```php
<?php

namespace App\Providers;

use App\Dto\Address\AddressDto;
use App\Dto\DimensionDto;
use App\Dto\Quote\FreightDto;
use App\Dto\Quote\ItemDto;
use App\Dto\Quote\PackageDto;
use Illuminate\Support\ServiceProvider;
use Fynix\RuleSet;
use Fynix\ValidationRegistry;

class ValidationRuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        self::registerDimensionValidation();
        self::registerItemValidation();
        self::registerAddressValidation();
        self::registerShippingValidation();
        self::registerPackageValidation();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    private static function registerDimensionValidation(): void {
        ValidationRegistry::register(DimensionDto::class, static function (RuleSet $rules): array {
            return [
                $rules->number('lengthCm')->min(1)->max(1800),
                $rules->number('widthCm')->min(1)->max(1800),
                $rules->number('heightCm')->min(1)->max(2000),
                $rules->number('weightKg')->min(1)->max(1000),
            ];
        });
    }

    private static function registerItemValidation() : void
    {
       ValidationRegistry::register(ItemDto::class, static function (RuleSet $rules): array {
            return [
                $rules->string('description'),
                $rules->object('dimension', DimensionDto::class),
           ];
       });
    }

    private static function registerAddressValidation() : void 
    {
        ValidationRegistry::register(AddressDto::class, static function (RuleSet $rules): array {
            return [
            $rules->string('suburb'),
            $rules->number('postcode')->length(2, 10),
            $rules->string('state')->length(2, 6),
            $rules->string('countryCode')->length(2, 4),
            ];
        });
    }

    private static function registerShippingValidation(): void 
    {
        ValidationRegistry::register(FreightDto::class, static function (RuleSet $rules): array {
            return[
            $rules->objectArray('packages', PackageDto::class),
            $rules->string('customerName')->length(0, 50)->optional(),
            $rules->object('pickupAddress', AddressDto::class),
            $rules->object('deliveryAddress', AddressDto::class),
            ];
        });
    }

    private static function registerPackageValidation(): void {
        ValidationRegistry::register(PackageDto::class, static function (RuleSet $rules): array {
            return [
            $rules->string('type'),
            $rules->string('description')->length(0, 50)->optional(),
            $rules->object('dimensions', DimensionDto::class),
            $rules->objectArray('items', ItemDto::class),
            ];
        });
    }
}

```

- and wherever needed just call with the dto class instance
```php
<?php
    $dto = DtoMapper::toFreightDto($args);
    $errors = ValidationHandler::validate($dto);
    $flattenedErrors = ValidationHandler::flattenValidationErrors($errors);
    
    if(count($errors) > 0) 
        return;
```
---

## Core Classes and Their Roles

### Validator
The central utility for validating data objects against rules. It provides static methods to retrieve validation errors for a given object and set of rules. Handles nested and array validation, returning errors as either strings or `ValidationError` objects.

### ValidationHandler
Orchestrates the validation process for single objects, arrays, or associative arrays. Supports error normalization (flattening nested error arrays to dot notation for easy form binding). Example:
```php
$errors = ValidationHandler::validate($user);
$flatErrors = ValidationHandler::flattenValidationErrors($errors);
```

### ValidationRegistry
Implements a registry pattern for associating classes with their validation rules. Register rules for a class and retrieve them dynamically during validation. Example:
```php
ValidationRegistry::register(User::class, static function (RuleSet $rules): array {
    return [
        $rules->string('firstName'),
        $rules->email('email'),
        // ...
    ];
});
```

### ValidationError
Represents a validation error, including the rule, error message, and field name. Used for structured error reporting and debugging.
---

## Extending the Library

You can create your own custom validators by extending `ValidatorBase` and implementing the protected `validateValue($fieldValue)` method. The public `validate()` method runs the complete shared pipeline before calling the custom type check. Add validator-specific fluent methods when your custom validator needs extra constraints.

```php
class CustomValidator extends ValidatorBase {
    protected function validateValue(mixed $fieldValue): ?ValidationError {
        // Custom validation logic
    }
}
```

## Best Practices & Advanced Patterns
- **Centralize Validation Logic**: Use `ValidationRegistry` to keep validation rules organized and reusable for each class.
- **Normalize Errors for UI**: Use `ValidationHandler::flattenValidationErrors()` to flatten errors for form binding and display.
- **Custom Validators**: Extend `ValidatorBase` for domain-specific validation needs.
- **Fluent Configuration**: Add chainable methods to custom validators for constraints that only they can enforce.
- **Batch Validation**: Validate multiple objects at once with `ValidationHandler::validateMany()` or associative arrays with `validateManyAssoc()`.

## Installation

```bash
composer require bishalshrestha/fynix
```
--- 

## Migration from v1 and v2

Versions 1 and 2 used public validator constructors and, in v2, the `Rules::for()` builder. Those APIs are no longer supported in v3. Replace them with the v3 `Rule`, `Rule::on()`, and `RuleSet` entry points. The fluent constraints and `UsernameValidator::uniqueUsing()` behavior remain available through those entry points.

## Migration to v3

Version 3 is a deliberate breaking release. Public validator constructors are removed and validators must be created through `Rule`, `ScopedRule`, or `RuleSet`.

### Validator construction

```php
Rule::string('name');
Rule::on(User::class)->string('name');
```

### `Rules::for()` removal

The v1/v2 builder APIs are removed entirely. Use `Rule::on()` or a static `RuleSet` registry closure:

```php
$rules = [Rule::on(User::class)->string('name')->min(2)];
```

### Registry closures

`ValidationRegistry` remains static. The closure argument changes from the DTO instance to `RuleSet`; instance-aware rule registration is not preserved:

```php
ValidationRegistry::register(User::class, static fn(RuleSet $rules): array => [
    $rules->string('name'),
]);
```

### Immutable fluent methods

Fluent methods return new instances in v3. Chaining is recommended:

```php
$validator = Rule::string('name')->min(2)->max(50);
```

Code that configured a validator across separate statements must reassign the result:

```php
$validator = Rule::string('name');
$validator = $validator->min(2);
```

### Composable rules

`AllOf`, `AnyOf`, and `Not` are composable rules, not validator classes. They are intentionally exempt from the protected-validator-constructor rule and may be constructed directly:

```php
$rule = new AllOf([Rule::string('name')->min(2), Rule::string('name')->max(50)]);
$alternative = new AnyOf([Rule::email('contact'), Rule::phoneNumber('contact')]);
$negated = new Not(Rule::string('status'), 'This status is not allowed.');
```

---

## Testing

PHPUnit tests are provided with the package:

```bash
composer test
composer analyse
```

If PHP and Composer are not installed locally, run the same checks in Docker:

```bash
docker compose run --rm test
```

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a feature branch (`git checkout -b feature-name`).
3. Make your changes and commit them with clear messages.
4. Run tests to ensure nothing is broken.
5. Submit a pull request explaining your changes.

For bug reports or feature requests, please open an issue on GitHub.

## Funding & Sponsorship

`fynix` is an open-source project maintained with care to deliver a reliable and extensible validation engine for PHP developers.  
If you or your organization find this project valuable, please consider supporting its development. Your sponsorship helps sustain long-term maintenance, improve features and documentation, and keep the library freely available to the community.  

As a token of appreciation, sponsors may have their logo and link featured in the project README and documentation site.  
Priority support and early access to planned features may also be offered where appropriate.  

### Support Options
[![GitHub Sponsors](https://img.shields.io/badge/GitHub%20Sponsors-Become%20a%20Sponsor-blueviolet?logo=githubsponsors&style=flat-square)](https://github.com/sponsors/shrestha-bishal)  
[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-Support%20Developer-yellow?logo=buymeacoffee&style=flat-square)](https://www.buymeacoffee.com/shresthabishal)  
[![Thanks.dev](https://img.shields.io/badge/Thanks.dev-Appreciate%20Open%20Source-29abe0?logo=github&style=flat-square)](https://thanks.dev/gh/shrestha-bishal)  

---

## License

This project is licensed under the [MIT License](./LICENSE).  

---

## Author

**Bishal Shrestha**  

[![GitHub](https://img.shields.io/badge/GitHub-Profile-black?logo=github)](https://github.com/shrestha-bishal)  
[![Repo](https://img.shields.io/badge/Repository-GitHub-black?logo=github)](https://github.com/shrestha-bishal/fynix)  
[Website](https://fynixphp.netlify.app)

© 2025 Bishal Shrestha, All rights reserved  

[![Packagist](https://img.shields.io/badge/Packagist-View%20Package-orange?logo=packagist&style=flat-square)](https://packagist.org/packages/bishalshrestha/fynix)
