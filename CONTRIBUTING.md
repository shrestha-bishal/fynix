# Contributing Guide

Thank you for your interest in contributing to **php-validation-core**.  
This project values high-quality, reliable PHP code and thoughtful collaboration.  
This guide outlines the standards and steps for reporting issues, contributing code, or improving documentation.

---

## Getting Started

1. **Fork** the repository on GitHub.  
2. **Clone** your fork:
   ```bash
   git clone https://github.com/shrestha-bishal/php-validation-core.git
   cd php-validation-core
   ```
3. **Install dependencies**:
   ```bash
   composer install
   ```
4. **Run the test suite**:
   ```bash
   composer test
   ```

---

## Development Workflow

### Composer Scripts

- `composer build` – Builds autoload and prepares the library for distribution.  
- `composer test` – Runs the full PHPUnit test suite.  
- `composer analyse` – Runs static analysis (if configured).  
- `composer format` – Formats code according to the project’s coding standards.

### Testing

All contributions must include or update unit tests located in the `tests/` directory.  
We use [PHPUnit](https://phpunit.de/) for testing.

Ensure tests are:

- Deterministic (no external API or network dependencies)  
- Clearly named and easy to understand  
- Cover edge cases (especially when adding new validation rules, nested object validation, or data structure handling)

---

## Coding Standards

- Written in **PHP 8.0+**  
- Follows **PSR-12** coding style  
- Uses **strict types** where applicable  
- Encourages modular, extensible, and reusable design  
- Favors clarity and minimal external dependencies  

> *Note: Please run `composer format` or any configured linter before submitting a PR to maintain consistency.*

---

## Submitting a Pull Request

1. Create a new feature branch:
   ```bash
   git checkout -b feature/my-enhancement
   ```
2. Make your changes and commit them with a clear, descriptive message.  
3. Push your branch to your fork:
   ```bash
   git push origin feature/my-enhancement
   ```
4. Open a Pull Request:
   - Use a descriptive title and summary.  
   - Reference related issues (e.g., `Fixes #12`).  
   - Ensure all tests and checks pass.  
   - Include tests for any new features or bug fixes.

---

## Reporting Bugs or Issues

Please use the [GitHub Issues](https://github.com/shrestha-bishal/php-validation-core/issues) page and include:

- A clear title and summary  
- Steps to reproduce the issue  
- Expected vs actual behavior  
- Relevant code snippets or test cases (if applicable)

---

## Suggesting Enhancements

We welcome feature requests and ideas. When suggesting enhancements, please describe:

- The problem you’re solving  
- Why it belongs in this library  
- Any proposed API changes, examples, or validation use cases  

---

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE), the same as this project.

---

Thank you for contributing to **php-validation-core**.  
Your efforts help make this library more robust, reliable, and developer-friendly.
