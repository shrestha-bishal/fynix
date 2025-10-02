# PHP Validation Core

**php-validation-core** is a modern, extensible PHP library for validating data types, files, and complex/nested objects. Designed for high performance, flexibility, and developer productivity, it is ideal for modern PHP applications and frameworks.

## Table of Contents
- [Features](#features)
- [Architecture Overview](#architecture-overview)
- [Validator Classes](#validator-classes)
- [Validator Options](#validator-options)
- [Validation Matrix](#validation-matrix)
- [Usage Examples](#usage-examples) 
- [Advanced Usage](#advanced-usage)
- [Extending the Library](#extending-the-library)
- [Installation](#installation)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features
- **Comprehensive Validation**: Strings, numbers, emails, phone numbers, passwords, images, arrays of images, nested objects, and arrays of objects.
- **Extensible Architecture**: Easily add custom validation rules or extend built-in validators.
- **Validator Options**: Fine-grained control over required fields, length, numeric ranges, file types, and more.
- **Nested & Array Validation**: Validate nested objects and arrays of objects using registered rules.
- **Error Normalization**: Flatten nested error structures for easy form binding.
- **Centralized Registry**: Register and retrieve validation rules for any class.
- **Open Source**: MIT-licensed and open for contributions.

## Architecture Overview

The library is organized into several core components:
- **Validator Classes**: Each validator encapsulates logic for a specific data type or structure.
- **Validator Options**: Option classes provide configuration for validators, such as length, required status, and custom constraints.
- **ValidationHandler**: Orchestrates validation, supports batch and associative validation, and error normalization.
- **ValidationRegistry**: Central registry for registering and retrieving validation rules for custom classes.
- **ValidationError**: Standardized error object for all validators.


## Validator Classes

| Validator                | Description                                                                                   | Options Class                   | Key Options                                                                                   |
|--------------------------|-----------------------------------------------------------------------------------------------|----------------------------------|----------------------------------------------------------------------------------------------|
| StringValidator          | Validates string type, length, nullability, and excludes HTML tags.                           | StringValidationOptions          | `length`, `isRequired`, `includeGenericValidation`                                            |
| NumberValidator          | Validates numeric type and enforces min/max value constraints.                                | NumberValidationOptions          | `length`, `number` (min/max), `isRequired`, `includeGenericValidation`                        |
| EmailValidator           | Validates email format, DNS, uniqueness, and structure.                                       | EmailValidationOptions           | `length`, `isUsername`, `isRequired`, `includeGenericValidation`                              |
| PhoneNumberValidator     | Validates phone number format, allowed symbols, and length.                                   | PhoneNumberValidationOptions     | `length`, `isRequired`, `includeGenericValidation`                                            |
| PasswordValidator        | Enforces password strength: uppercase, lowercase, number, special character, length.          | PasswordValidationOptions        | `length`, `isRequired`, `includeGenericValidation`                                            |
| ImageValidator           | Validates a single image file: size, extension, and actual image content.                     | ImageValidationOptions           | `numImage`, `maxFileSizeMB`, `isRequired`, `includeGenericValidation`                         |
| ImagesValidator          | Validates an array of image files, each using ImageValidator.                                 | ImageValidationOptions           | `numImage`, `maxFileSizeMB`, `isRequired`, `includeGenericValidation`                         |
| ObjectValidator          | Validates a nested object property using registered rules for its class.                      | ObjectValidationOptions          | `validateOnNull`                                                                             |
| ObjectArrayValidator     | Validates an array of objects, each using registered rules for its class.                     | N/A                              | N/A                                                                                          |

## Validator Options

- **ValidationOptionsBase**: Common base for all options classes. Properties include:
  - `includeGenericValidation`: Enable/disable generic validation (nullability, length, etc.)
  - `fieldType`: Type of field (string, number, email, etc.)
  - `isRequired`: Whether the field is required.
  - `length`: Array with `min` and `max` constraints.

- **StringValidationOptions**: Controls string length, required status, and generic validation.
- **NumberValidationOptions**: Controls string length, numeric min/max, required status, and generic validation.
- **EmailValidationOptions**: Controls string length, username uniqueness, required status, and generic validation.
- **PhoneNumberValidationOptions**: Controls phone number length, required status, and generic validation.
- **PasswordValidationOptions**: Controls password length, required status, and generic validation.
- **ImageValidationOptions**: Controls number of images, max file size (MB), required status, and generic validation.
- **ObjectValidationOptions**: Controls whether to validate when the object is null.

### ValidationOptions Classes

All validators accept an options object to configure their behavior. These options classes allow you to fine-tune validation logic for each field type.

#### ValidationOptionsBase
Abstract base class for all options. Common properties:
- `includeGenericValidation`: Enable/disable generic validation (nullability, length, etc.)
- `fieldType`: Type of field (string, number, email, etc.)
- `isRequired`: Whether the field is required.
- `length`: Array with `min` and `max` constraints.

#### StringValidationOptions
Configure string validation:
- `length`: Min/max length (default: 2-50)
- `isRequired`: Field required (default: true)
- `includeGenericValidation`: Enable generic checks (default: true)

#### NumberValidationOptions
Configure number validation:
- `length`: Min/max string length
- `number`: Min/max numeric value
- `isRequired`, `includeGenericValidation`

#### EmailValidationOptions
Configure email validation:
- `length`: Min/max length
- `isUsername`: Check for username uniqueness
- `isRequired`, `includeGenericValidation`

#### PhoneNumberValidationOptions
Configure phone number validation:
- `length`: Min/max length
- `isRequired`, `includeGenericValidation`

#### PasswordValidationOptions
Configure password validation:
- `length`: Min/max length
- `isRequired`, `includeGenericValidation`

#### ImageValidationOptions
Configure image validation:
- `numImage`: Min/max number of images
- `maxFileSizeMB`: Maximum file size in MB
- `isRequired`, `includeGenericValidation`

#### ObjectValidationOptions
Configure nested object validation:
- `validateOnNull`: Whether to validate if the object is null

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

## Usage Examples

### Basic String Validation
```php
use PhpValidationCore\Validators\StringValidator;
use PhpValidationCore\ValidationOptions\StringValidationOptions;

$options = new StringValidationOptions(['min' => 2, 'max' => 50], true);
$stringValidator = new StringValidator('First Name', 'firstName', $options);
```

### Email Validation with Options
```php
use PhpValidationCore\Validators\EmailValidator;
use PhpValidationCore\ValidationOptions\EmailValidationOptions;

$emailOptions = new EmailValidationOptions(['min' => 6, 'max' => 100], true, true, true);
$emailValidator = new EmailValidator('Email', 'email', $emailOptions);
```

### Number Validation
```php
use PhpValidationCore\Validators\NumberValidator;
use PhpValidationCore\ValidationOptions\NumberValidationOptions;

$numberOptions = new NumberValidationOptions(['min' => 1, 'max' => 30], true, true, ['min' => 18, 'max' => 99]);
$numberValidator = new NumberValidator('Age', 'age', $numberOptions);
```

### Password Validation
```php
use PhpValidationCore\Validators\PasswordValidator;
use PhpValidationCore\ValidationOptions\PasswordValidationOptions;

$passwordOptions = new PasswordValidationOptions(['min' => 8, 'max' => 30], true);
$passwordValidator = new PasswordValidator('Password', 'password', $passwordOptions);
```

### Image Validation
```php
use PhpValidationCore\Validators\ImageValidator;
use PhpValidationCore\ValidationOptions\ImageValidationOptions;

$imageOptions = new ImageValidationOptions(['min' => 1, 'max' => 1], true, false, 5);
$imageValidator = new ImageValidator('Profile Picture', 'profilePic', $imageOptions);
```

### Nested Object Validation
```php
use PhpValidationCore\Validators\ObjectValidator;
use PhpValidationCore\ValidationOptions\ObjectValidationOptions;

$objectOptions = new ObjectValidationOptions();
$objectValidator = new ObjectValidator(UserAddress::class, 'address', $objectOptions);
```

### Array of Objects Validation
```php
use PhpValidationCore\Validators\ObjectArrayValidator;

$objectArrayValidator = new ObjectArrayValidator(FreightItemDto::class, 'items');
```

### Example: Full User Registration Validation

Below is a practical example showing how to use the library for a user registration form with multiple fields and nested validation:

```php
use PhpValidationCore\Validators\StringValidator;
use PhpValidationCore\Validators\EmailValidator;
use PhpValidationCore\Validators\PasswordValidator;
use PhpValidationCore\Validators\ObjectValidator;
use PhpValidationCore\ValidationRegistry;
use PhpValidationCore\ValidationHandler;

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

    public static function getValidationRules($instance) {
        return [
            new StringValidator('First Name', 'firstName'),
            new EmailValidator('Email', 'email'),
            new PasswordValidator('Password', 'password'),
            new ObjectValidator(UserAddress::class, 'address'),
        ];
    }
}

ValidationRegistry::register(User::class, [User::class, 'getValidationRules']);
ValidationRegistry::register(UserAddress::class, function($instance) {
    return [
        new StringValidator('Street', 'street'),
        new StringValidator('City', 'city'),
        new StringValidator('Postcode', 'postcode'),
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

## Advanced Usage

### Batch Validation
```php
use PhpValidationCore\ValidationHandler;

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
$flatErrors = ValidationHandler::normaliseValidationErrors($errors);
```

### Registering Custom Validation Rules
```php
use PhpValidationCore\ValidationRegistry;

ValidationRegistry::register(User::class, function($instance) {
    return [
        new StringValidator('First Name', 'firstName'),
        new EmailValidator('Email', 'email'),
        // ... other rules
    ];
});
```

### Reusable Validation Instances - More modern and Advanced Way
`php-validation-core` allows you to define reusable validation rules for your **data transfer objects (DTOs)** using the `ValidationRegistry`.
The `ValidationRuleServiceProvider` demonstrates how to register validation rules for multiple DTOs in a structured and type-safe way.

```php
<?php

namespace App\Providers;

use App\Dto\Address\AddressDto;
use App\Dto\DimensionDto;
use App\Dto\Quote\FreightDto;
use App\Dto\Quote\ItemDto;
use App\Dto\Quote\PackageDto;
use Illuminate\Support\ServiceProvider;
use PhpValidationCore\ValidationOptions\NumberValidationOptions;
use PhpValidationCore\ValidationOptions\StringValidationOptions;
use PhpValidationCore\ValidationRegistry;
use PhpValidationCore\Validators\NumberValidator;
use PhpValidationCore\Validators\ObjectArrayValidator;
use PhpValidationCore\Validators\ObjectValidator;
use PhpValidationCore\Validators\StringValidator;

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

    private static function registerDimensionValidation() : void {
        ValidationRegistry::register(DimensionDto::class, function(DimensionDto $dimension) {
            return [
                new NumberValidator('Length', 'lengthCm', new NumberValidationOptions(number: [1, 1800])),
                new NumberValidator('Width', 'widthCm', new NumberValidationOptions(number: [1, 1800])),
                new NumberValidator('Height', 'heightCm', new NumberValidationOptions(number: [1, 2000])),
                new NumberValidator('Weight', 'weightKg', new NumberValidationOptions(number: [1, 1000]))
            ];
        });
    }

    private static function registerItemValidation() : void
    {
       ValidationRegistry::register(ItemDto::class, function (ItemDto $dto) {
            return [
                new StringValidator('Description', 'description'),
                new ObjectValidator('dimension', DimensionDto::class),
           ];
       });
    }

    private static function registerAddressValidation() : void 
    {
        ValidationRegistry::register(AddressDto::class, function(AddressDto $dto) {
            return [
                new StringValidator('Suburb', 'suburb'),
                new NumberValidator('Postcode', 'postcode', new NumberValidationOptions(length: [2, 10])),
                new StringValidator('State', 'state', new StringValidationOptions(length: [2, 6])),
                new StringValidator('Country', 'countryCode', new StringValidationOptions(length: [2, 4]))
            ];
        });
    }

    private static function registerShippingValidation(): void 
    {
        ValidationRegistry::register(FreightDto::class, function(FreightDto $dto) {
            return[
                new ObjectArrayValidator('packages', PackageDto::class),
                new StringValidator('Customer name', 'customerName', new StringValidationOptions(length: [0, 50], isRequired: false)),
                new ObjectValidator('pickupAddress', AddressDto::class),
                new ObjectValidator('deliveryAddress', AddressDto::class),
            ];
        });
    }

    private static function registerPackageValidation(): void {
        ValidationRegistry::register(PackageDto::class, function(PackageDto $dto) {
            return [
                new StringValidator('Package Type', 'type'),
                new StringValidator('Description', 'description', new StringValidationOptions(length: [0, 50], isRequired: false)),
                new ObjectValidator('dimensions', DimensionDto::class),
                new ObjectArrayValidator('items', ItemDto::class)
            ];
        });
    }
}

```


## Core Classes and Their Roles

### Validator
The central utility for validating data objects against rules. It provides static methods to retrieve validation errors for a given object and set of rules. Handles nested and array validation, returning errors as either strings or `ValidationError` objects.

### ValidationHandler
Orchestrates the validation process for single objects, arrays, or associative arrays. Supports error normalization (flattening nested error arrays to dot notation for easy form binding). Example:
```php
$errors = ValidationHandler::validate($user);
$flatErrors = ValidationHandler::normaliseValidationErrors($errors);
```

### ValidationRegistry
Implements a registry pattern for associating classes with their validation rules. Register rules for a class and retrieve them dynamically during validation. Example:
```php
ValidationRegistry::register(User::class, function($instance) {
    return [
        new StringValidator('First Name', 'firstName'),
        new EmailValidator('Email', 'email'),
        // ...
    ];
});
```

### ValidationError
Represents a validation error, including the rule, error message, and field name. Used for structured error reporting and debugging.

---

## Extending the Library

You can create your own custom validators by extending `ValidatorBase` and implementing the `validate($fieldValue)` method. Custom option classes can also be created by extending `ValidationOptionsBase`.

```php
class CustomValidator extends ValidatorBase {
    public function validate($fieldValue) : ?ValidationError {
        // Custom validation logic
    }
}
```

## Validators: Detailed Documentation

### ValidatorBase
Abstract base for all validators. Implements generic validation (nullability, length, HTML exclusion) and requires child classes to implement `validate($fieldValue)` for specific logic.

### StringValidator
Validates string type, length, nullability, and excludes HTML tags. Usage:
```php
$options = new StringValidationOptions(['min' => 2, 'max' => 50], true);
$validator = new StringValidator('First Name', 'firstName', $options);
```

### NumberValidator
Validates numeric type and enforces min/max value constraints. Usage:
```php
$options = new NumberValidationOptions(['min' => 1, 'max' => 30], true, true, ['min' => 18, 'max' => 99]);
$validator = new NumberValidator('Age', 'age', $options);
```

### EmailValidator
Validates email format, DNS, uniqueness, and structure. Usage:
```php
$options = new EmailValidationOptions(['min' => 6, 'max' => 100], true, true, true);
$validator = new EmailValidator('Email', 'email', $options);
```

### PhoneNumberValidator
Validates phone number format, allowed symbols, and length. Usage:
```php
$options = new PhoneNumberValidationOptions(['min' => 10, 'max' => 12], true);
$validator = new PhoneNumberValidator('Phone', 'phoneNumber', $options);
```

### PasswordValidator
Enforces password strength: uppercase, lowercase, number, special character, length. Usage:
```php
$options = new PasswordValidationOptions(['min' => 8, 'max' => 30], true);
$validator = new PasswordValidator('Password', 'password', $options);
```

### ImageValidator
Validates a single image file: size, extension, and actual image content. Usage:
```php
$options = new ImageValidationOptions(['min' => 1, 'max' => 1], true, false, 5);
$validator = new ImageValidator('Profile Picture', 'profilePic', $options);
```

### ImagesValidator
Validates an array of image files, each using ImageValidator. Usage:
```php
$options = new ImageValidationOptions(['min' => 1, 'max' => 5], true, false, 5);
$validator = new ImagesValidator('Gallery', 'galleryImages', $options);
```

### ObjectValidator
Validates a nested object property using registered rules for its class. Usage:
```php
$options = new ObjectValidationOptions();
$validator = new ObjectValidator(UserAddress::class, 'address', $options);
```

### ObjectArrayValidator
Validates an array of objects, each using registered rules for its class. Usage:
```php
$validator = new ObjectArrayValidator(FreightItemDto::class, 'items');
```

## Best Practices & Advanced Patterns

- **Centralize Validation Logic**: Use `ValidationRegistry` to keep validation rules organized and reusable for each class.
- **Normalize Errors for UI**: Use `ValidationHandler::normaliseValidationErrors()` to flatten errors for form binding and display.
- **Custom Validators**: Extend `ValidatorBase` for domain-specific validation needs.
- **Custom Options**: Extend `ValidationOptionsBase` to add new configuration parameters for your validators.
- **Batch Validation**: Validate multiple objects at once with `ValidationHandler::validateMany()` or associative arrays with `validateManyAssoc()`.

## Installation

```bash
composer require bishalshrestha/php-validation-core
```

## Testing

Unit tests are provided using PHPUnit:

```bash
vendor/bin/phpunit tests/
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

`php-validation-core` is an open-source project maintained with care to deliver a reliable and extensible validation engine for PHP developers.  
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
[![Repo](https://img.shields.io/badge/Repository-GitHub-black?logo=github)](https://github.com/shrestha-bishal/php-validation-core)  
[Website](https://php-validation-core.netlify.app) *(coming soon)*  

© 2025 Bishal Shrestha, All rights reserved  

[![Packagist](https://img.shields.io/badge/Packagist-View%20Package-orange?logo=packagist&style=flat-square)](https://packagist.org/packages/bishalshrestha/php-validation-core)
