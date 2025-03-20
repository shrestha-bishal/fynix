### Validate PHP Core

`validatephp-core` is a lightweight, robust PHP validation library that provides essential validation functionality for common data types such as strings, emails, numbers, phone numbers, image or images, passwords and more with the flexibility to validate optional fields as well. Designed to be simple, extensible, and highly efficient, this package helps ensure data integrity and security in your PHP applications.

Whether you're building a small project or a large application, `validatephp-core` provides a straightforward API for integrating validation into your workflow. It supports customizable rules and can be easily extended to meet specific validation needs.

Key Features:
Comprehensive Validation: Handles common validation cases like strings, emails, numbers, phone numbers, image or images, passwords and more with the flexibility to validate optional fields
Extensible: Add custom validation rules or extend the library for more specialized use cases.
Easy to Use: Simple library for quick integration into any PHP project.
Lightweight and Fast: Optimized for performance without unnecessary overhead.
Open Source: MIT-licensed, open for contributions.

#### Validation Chart
| Validation               | String | Number | Email | PhoneNumber | Password | ConfirmPassword | Image | Images
|--------------------------|--------|--------|-------|-------------|----------|----------------|✓  ✓  
| Nullability             | ✓      | ✓      | ✓     | ✓           | ✓        | ✓              |✓ 
| HTML                    | ✓      |        | ✓     |             |          |                |
| min - max Length        | ✓      | ✓      | ✓     | ✓           | ✓        | ✓              |
| isString                | ✓      |        |       |             |          |                |
| isNumeric               |        | ✓      |       | ✓           |          |                |
| min - max number        |        | ✓      |       |             |          |                |
| isEmail                 |        |        | ✓     |             |          |                |
| DNS                     |        |        | ✓     |             |          |                |
| Count '@'               |        |        | ✓     |             |          |                |
| Count '.'               |        |        | ✓     |             |          |                |
| validatePhonePattern    |        |        |       | ✓           |          |                |
| contains upper case     |        |        |       |             | ✓        | ✓              |
| contains lower case     |        |        |       |             | ✓        | ✓              |✓  
| contains one number     |        |        |       |             | ✓        | ✓              |
| contains special char   |        |        |       |             | ✓        | ✓              |
| no spaces               |        |        |       |             | ✓        | ✓              |
| !=                      |        |        |       |             | ✓        | ✓              |
| validate with existing  |        |        | ✓     |             |          |                |
