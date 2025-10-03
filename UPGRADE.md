# Upgrade guide — php-validation-core

This file lists important breaking changes and migration steps for upgrading to v1.0.1.

Date: 2025-10-03

## Summary

v1.0.1 includes API cleanup and documentation fixes. The principal breaking changes are:

- Object validator constructor signatures changed.
- Error-normalization helper renamed.
- `ObjectValidationOptions` has been removed from examples (object validators no longer accept options).

## Breaking changes & migration steps

1) ObjectValidator / ObjectArrayValidator constructor signature

-- Before (pre-1.0.1):

```php
// old: class-first (or passed options third)
$validator = new ObjectValidator(UserAddress::class, 'address', $options);
$items = new ObjectArrayValidator(FreightItemDto::class, 'items');
```

-- After (v1.0.1+):

```php
// new: property name first, class second, no options param
$validator = new ObjectValidator('address', UserAddress::class);
$items = new ObjectArrayValidator('items', FreightItemDto::class);
```

Migration: Update all call sites where `ObjectValidator` / `ObjectArrayValidator` are instantiated. Remove any third `options` parameter — object validators no longer accept an options object.

2) Error normalization helper renamed

-- Before (pre-1.0.1):

```php
$flat = ValidationHandler::normaliseValidationErrors($errors);
```

-- After (v1.0.1+):

```php
$flat = ValidationHandler::flattenValidationErrors($errors);
// or for a single instance:
$flat = ValidationHandler::validateAndFlatten($instance);
```

Migration: Replace all calls to `normaliseValidationErrors` with `flattenValidationErrors` or use `validateAndFlatten()` for validate+flatten.

3) Notes on validators & options

- Validators still accept typed options objects (e.g. `StringValidationOptions`, `NumberValidationOptions`, `ImageValidationOptions`). Use those option objects to configure validators.
- `ValidationError` instances contain `$rule`, `$message`, and `$field_name` (derived from rule->propertyName).

## Example quick upgrade snippet

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

For a full list of changes, see [CHANGELOG.md](./CHANGELOG.md).