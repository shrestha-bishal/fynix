# Changelog  

All notable changes to this project are documented in this file.  

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

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