# Changelog

All notable changes to this project are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).  

## [1.0.1] - 2025-10-03

### Added
- Added `UPGRADE.md` with migration instructions and examples for upgrading to v1.0.1.
- Added `CHANGELOG.md` (this file) summarizing release notes and migration guidance.

### Changed
- Documentation: updated `README.md` examples to match the current public API.
- ValidationHandler: error-normalization helper is now referenced as `flattenValidationErrors` in docs and examples.
- Object validators: `ObjectValidator` / `ObjectArrayValidator` examples updated to use `(propertyName, className)` constructor ordering.

### Fixed
- Fixed various README code samples that used outdated constructor signatures and helper names.

### Removed
- Removed references to a non-existent `ObjectValidationOptions` in examples. Object validators no longer accept an options object.

### Breaking Changes (public API surface)
- ObjectValidator / ObjectArrayValidator constructors: parameter ordering changed for examples and public usage. Update your code where you instantiate these validators.
  - Before (pre-1.0.1):
    ```php
    // old: class-first (or passed options third)
    $validator = new ObjectValidator(UserAddress::class, 'address', $options);
    $items = new ObjectArrayValidator(FreightItemDto::class, 'items');
    ```
  - After (v1.0.1+):
    ```php
    // new: property name first, class second, no options param
    $validator = new ObjectValidator('address', UserAddress::class);
    $items = new ObjectArrayValidator('items', FreightItemDto::class);
    ```

- Error normalization helper renamed (docs & examples):
  - Before: `ValidationHandler::normaliseValidationErrors($errors)`
  - After: `ValidationHandler::flattenValidationErrors($errors)`
  - Helper available: `ValidationHandler::validateAndFlatten($instance)` for single-instance validate+flatten convenience.

---

## Migration guide (summary)

If you are upgrading from a pre-1.0.1 version, please follow these steps:

1. Update all instantiations of `ObjectValidator` / `ObjectArrayValidator` to pass the property name first, then the class name. Remove any third `options` argument.

2. Replace calls to `ValidationHandler::normaliseValidationErrors()` with `ValidationHandler::flattenValidationErrors()`.

3. Update code and examples that referenced `ObjectValidationOptions` — that options class is no longer used with object validators in examples. Other validators still accept options objects (e.g., `StringValidationOptions`, `NumberValidationOptions`, `ImageValidationOptions`).

### Quick before/after

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

---

## Full upgrade notes (copied from `UPGRADE.md`)

The following section contains the more detailed notes included in `UPGRADE.md`.

> This file lists important breaking changes and migration steps for upgrading to v1.0.1.
>
> Date: 2025-10-03
>
> Summary
>
> v1.0.1 includes API cleanup and documentation fixes. The principal breaking changes are:
>
> - Object validator constructor signatures changed.
> - Error-normalization helper renamed.
> - `ObjectValidationOptions` has been removed from examples (object validators no longer accept options).
>
> Breaking changes & migration steps
>
> 1) ObjectValidator / ObjectArrayValidator constructor signature
>
> - Before (pre-1.0.1):
>
> ```php
> // old: class-first (or passed options third)
> $validator = new ObjectValidator(UserAddress::class, 'address', $options);
> $items = new ObjectArrayValidator(FreightItemDto::class, 'items');
> ```
>
> - After (v1.0.1+):
>
> ```php
> // new: property name first, class second, no options param
> $validator = new ObjectValidator('address', UserAddress::class);
> $items = new ObjectArrayValidator('items', FreightItemDto::class);
> ```
>
> Migration: Update all call sites where `ObjectValidator` / `ObjectArrayValidator` are instantiated. Remove any third `options` parameter — object validators no longer accept an options object.
>
> 2) Error normalization helper renamed
>
> - Before (pre-1.0.1):
>
> ```php
> $flat = ValidationHandler::normaliseValidationErrors($errors);
> ```
>
> - After (v1.0.1+):
>
> ```php
> $flat = ValidationHandler::flattenValidationErrors($errors);
> // or for a single instance:
> $flat = ValidationHandler::validateAndFlatten($instance);
> ```
>
> Migration: Replace all calls to `normaliseValidationErrors` with `flattenValidationErrors` or use `validateAndFlatten()` for validate+flatten.
>
> 3) Notes on validators & options
>
> - Validators still accept typed options objects (e.g. `StringValidationOptions`, `NumberValidationOptions`, `ImageValidationOptions`). Use those option objects to configure validators.
> - `ValidationError` instances contain `$rule`, `$message`, and `$field_name` (derived from rule->propertyName).
>
> Example quick upgrade snippet
>
> Before:
>
> ```php
> ValidationRegistry::register(User::class, function($instance) {
>     return [
>         new StringValidator('First Name', 'firstName'),
>         new ObjectValidator(UserAddress::class, 'address', new ObjectValidationOptions()),
>     ];
> });
> ```
>
> After:
>
> ```php
> ValidationRegistry::register(User::class, function($instance) {
>     return [
>         new StringValidator('First Name', 'firstName'),
>         new ObjectValidator('address', UserAddress::class),
>     ];
> });
> ```

For an upgrade guide, see [UPGRADE.md](./UPGRADE.md).