<?php 
namespace PhpValidationCore\Validators;

use PhpValidationCore\ValidationOptions\ObjectValidationOptions;

/**
 * Class ObjectValidator
 *
 * Validates a nested object (e.g., a DTO) by resolving and invoking its registered validation logic.
 */
class ObjectValidator {

    /**
     * @param string $propertyName The name of the property that holds the nested object.
     * @param string $registeredClassName The class name whose validator is registered in ValidationRegistry using register().
     */
    public function __construct(
        public string $className,
        public string $propertyName,
        public ?ObjectValidationOptions $options = null) 
        {
            $this->options ??= new ObjectValidationOptions();
        }
}