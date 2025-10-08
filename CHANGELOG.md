# Changelog  

All notable changes to this project are documented in this file.  

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).  

## [1.0.2] - 2025-10-08
### Added
- Repository and community metadata to improve contributor experience and security contactability:
  - .github/CODEOWNERS
  - .github/ISSUE_TEMPLATE/bug_report.md
  - .github/ISSUE_TEMPLATE/feature_request.md
  - CODE_OF_CONDUCT.md
  - CONTRIBUTING.md
  - SECURITY.md

### Changed
- No runtime or public API changes — this release contains repository housekeeping and documentation only.

### Notes
- Non-breaking patch release. No code changes required when upgrading from 1.0.1 → 1.0.2.
- Verify repository settings (issue templates and CODEOWNERS) and security contact info after publishing.

---


## [1.0.1] - 2025-10-03  

**Note:** This release increments the patch version but includes breaking changes.  
The goal is to clean up the API and examples before wider adoption.  

### Added  
- `UPGRADE.md` with detailed migration instructions and examples.  
- This `CHANGELOG.md` file for release tracking.  

### Changed  
- Documentation (`README.md`) updated to match the current public API.  
- `ValidationHandler`: error-normalization helper now named `flattenValidationErrors` in docs and examples.  
- Object validators: updated examples to use `(propertyName, className)` constructor ordering.  

### Fixed  
- Corrected outdated code samples in `README.md`.  

### Removed  
- References to `ObjectValidationOptions` in examples (object validators no longer accept options).  

### Breaking Changes (public API)  
- **ObjectValidator / ObjectArrayValidator constructors**  
  - Before:  
    ```php
    $validator = new ObjectValidator(UserAddress::class, 'address', $options);
    $items = new ObjectArrayValidator(FreightItemDto::class, 'items');
    ```  
  - After:  
    ```php
    $validator = new ObjectValidator('address', UserAddress::class);
    $items = new ObjectArrayValidator('items', FreightItemDto::class);
    ```  

- **Error normalization helper renamed**  
  - Before:  
    ```php
    $flat = ValidationHandler::normaliseValidationErrors($errors);
    ```  
  - After:  
    ```php
    $flat = ValidationHandler::flattenValidationErrors($errors);
    // or:
    $flat = ValidationHandler::validateAndFlatten($instance);
    ```  

---

## Migration guide  

If you are upgrading from a pre-1.0.1 version:  

1. Update all `ObjectValidator` / `ObjectArrayValidator` instantiations to pass the property name first, then the class. Remove any third `$options` argument.  
2. Replace `ValidationHandler::normaliseValidationErrors()` with `ValidationHandler::flattenValidationErrors()` (or `validateAndFlatten()`).  
3. Remove any usage of `ObjectValidationOptions` with object validators. Other validators still accept typed options (`StringValidationOptions`, `NumberValidationOptions`, etc.).  

**Quick before/after example:**  

Before:  
```php
ValidationRegistry::register(User::class, function($instance) {
    return [
        new StringValidator('First Name', 'firstName'),
        new ObjectValidator(UserAddress::class, 'address', new ObjectValidationOptions()),
    ];
});
```  

After:  
```php
ValidationRegistry::register(User::class, function($instance) {
    return [
        new StringValidator('First Name', 'firstName'),
        new ObjectValidator('address', UserAddress::class),
    ];
});
```  

For full details, see [UPGRADE.md](https://github.com/shrestha-bishal/php-validation-core/blob/v1.0.1/UPGRADE.md)