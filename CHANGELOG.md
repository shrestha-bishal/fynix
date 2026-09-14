# Changelog  

All notable changes to this project are documented in this file.  

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [3.1.0] - 2026-09-14

### Added

- Conditional rules now accept either a field/value pair or a closure predicate.
- Added `when()` to conditionally enable any validator using the owning object.
- Added closure support to `sameAs()`, `differentFrom()`, `requiredIf()`, `requiredUnless()`, `prohibitedIf()`, and `prohibitedUnless()`.

## [3.0.0] - 2026-09-11

### Added

- Added boolean, date-time, array, URL, UUID, integer, decimal, enum, file, IP address, and regex validators.
- Added per-item array validation through `ArrayValidator::each()`.
- Added generic `in()` and `notIn()` value constraints.
- Added cross-field constraints: `sameAs()`, `differentFrom()`, `requiredIf()`, `requiredUnless()`, `prohibitedIf()`, and `prohibitedUnless()`.
- Added Docker-based PHPUnit and PHPStan test execution.

### Breaking

- Replaced public validator construction with `Rule`, `ScopedRule`, and `RuleSet`.
- Validator fluent methods now return immutable clones instead of mutating the original instance.
- Replaced instance-based registry closures with `RuleSet` factories.
- Kept `ValidationRegistry` static; registry ownership and calls remain `ValidationRegistry::register(...)`.
- Added typed rule-definition exceptions, composable rules, and validation listeners.

### Fixed

- Fixed validator factory signatures for PHP 8.5 compatibility.
- Fixed fluent cloning for validators with subclass-specific state.
- Added explicit `RuleSet` factories for all concrete validators.

## [2.0.0] - 2026-09-11

### Added
- Added chainable validator configuration for lengths, numeric ranges, image counts, required fields, and image file sizes.
- Added structured validation error metadata with error codes, parameters, and `toArray()`.
- Added required/optional configuration for nested objects and object arrays.
- Added conditional requiredness through `isRequired(bool)` with `requiredState()` for rule inspection.
- Added all-errors validation through `validateFieldAll()` while preserving first-error `validateField()` compatibility.
- Added `UsernameValidator` with injectable `uniqueUsing()` support for database-backed uniqueness checks.
- Added Composer-autoloaded `Fynix\nameof()` for validated property names.
- Added callable `Rules::for()` fluent builder for composing DTO validation rules.
- Added PHPUnit coverage and the `composer test` script.

### Changed
- Removed the validation options class hierarchy in favor of fluent validator configuration.
- Email DNS verification is now opt-in through `verifyDomain()` and no longer requires network access by default.
- Removed the misleading username/database stub from `EmailValidator`.
- Image validation now checks the actual image file with `getimagesize()`.
- Minimum supported PHP version is now 8.1.

### Fixed
- Fixed cyclic nested-object traversal.
- Fixed missing and invalid nested object/array item reporting.
- Fixed numeric minimum/maximum validation when only one bound is configured.
- Added PHPStan 2 analysis and a PHP 8.1-8.4 CI matrix.

## [1.0.2] - 2025-10-21

### Added
- Added `archive` configuration in `composer.json` to exclude development files from distribution
- Added exclusion rules for development-related files and directories:
  - `.github/`, `.vscode/`, `tests/`, `vendor/`
  - Documentation files: `CHANGELOG.md`, `CODE_OF_CONDUCT.md`, `CONTRIBUTING.md`, `SECURITY.md`, `UPGRADE.md`
  - Git files: `.gitattributes`, `.gitignore`

### Changed
- No changes in this release.

### Fixed
- No bug fixes in this release.

### Security
- No security fixes in this release.

## [1.0.1] - 2025-10-13

### Added
- Added `.github/dependabot.yml` for automated dependency updates via Dependabot.
- Added `.github/workflows/ci.yml` for GitHub Actions CI workflow to run tests on push and pull requests.
- Added `"homepage": "https://fynixphp.netlify.app"` to `composer.json`.

### Changed
- Updated `composer.json` to include `"homepage"` field.
 Changed PSR-4 autoload namespace in `composer.json` from `PhpValidationCore\\` to `Fynix\\`.
 Changed author email in `composer.json` to `dev.bishal@outlook.com`.

### Fixed
- No bug fixes in this release.

### Security
- No security fixes in this release.