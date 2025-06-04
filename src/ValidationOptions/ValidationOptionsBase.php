<?php 
namespace PhpValidationCore\ValidationOptions;

/**
 * Class ValidationOptions
 *
 * Acts as an interface-like base class for validation option classes.
 * This class serves as a placeholder to define common properties
 * used by child classes that represent specific validation option sets.
 *
 * It does not implement any validation logic or constructor,
 * and is intended to be extended by concrete validation option classes.
 *
 * Properties:
 * - bool $includeGenericValidation: Whether to include generic validation rules.
 * - string $fieldType: The type of the field (e.g., 'string', 'number', 'email').
 * - bool $isRequired: Whether the field is required.
 * - ?array $length: Length constraints, with keys 'min' and 'max'.
 */

abstract class ValidationOptionsBase {
    public bool $includeGenericValidation;
    public string $fieldType;
    public bool $isRequired; 
    public array $length = ['min' => null, 'max' => null]; 
}