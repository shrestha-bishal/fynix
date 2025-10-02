# PHP Validation Core

**php-validation-core** is a modern, extensible PHP library for validating primitive data types, files, complex objects, and nested objects, including arrays of objects. Designed for high performance and flexibility, it enables developers to implement robust validation logic across modern PHP applications and frameworks.

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

// Nested DTO structure
$freight = new FreightDto();
$freight->packages[] = (new PackageDto())->items[] = new ItemDto();
$freight->pickupAddress = new AddressDto();
$freight->deliveryAddress = new AddressDto();
```

### Setting Up Validation
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

![Validation Example](https://github.com/user-attachments/assets/e2454460-9936-4aa9-877a-fbff3d7267a9)  
*Original validation output*

![Flattened Validation Example](https://github.com/user-attachments/assets/941e4f32-ad6f-4817-9477-e2e1870a4257)  
*Flattened validation output*

---

## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Testing](#testing)
- [Contributing](#contributing)
- [Support the Project](#funding-&-sponsorship)
- [License](#license)
- [Author](#author)

## Features
- **Comprehensive Validation**: Strings, numbers, emails, phone numbers, passwords, images, arrays of images, nested objects, and arrays of objects.
- **Extensible Architecture**: Easily add custom validation rules or extend built-in validators.
- **Validator Options**: Fine-grained control over required fields, length, numeric ranges, file types, and more.
- **Nested & Array Validation**: Validate nested objects and arrays of objects using registered rules.
- **Error Normalization**: Flatten nested error structures for easy form binding.
- **Centralized Registry**: Register and retrieve validation rules for any class.
- **Open Source**: MIT-licensed and open for contributions.

--- 

## Installation

```bash
composer require bishalshrestha/php-validation-core
```
--- 

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
