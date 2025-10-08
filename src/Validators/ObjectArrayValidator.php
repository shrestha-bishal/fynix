<?php 
namespace Fynix\Validators;

/**
 * Class ObjectArrayValidator
 *
 * Validates a property that contains an array of objects, where each object is validated
 * using the registered rules for its class (e.g. DTOs with ValidationRegistry).
 *
 * Usage:
 * ```
 * new ObjectArrayValidator('items', FreightItemDto::class)
 * ```
 *
 * Requirements:
 * - The target property must be an array.
 * - Each element in the array must be an object of the specified type.
 * - Validation rules must be registered for the given object class via ValidationRegistry.
 *
 * @package YourNamespace\Validators
 */
class ObjectArrayValidator {
    public function __construct(
        public string $propertyName,
        public string $className) {}
}