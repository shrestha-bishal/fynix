<?php 
namespace Fynix\Validators;

/**
 * Class ObjectValidator
 *
 * Validates a nested object (e.g., a DTO) by resolving and invoking its registered validation logic.
 */
class ObjectValidator {

    private bool $isRequired = true;

    /**
     * @param string $propertyName The name of the property that holds the nested object.
    * @param string $className The class name whose validator is registered in ValidationRegistry using register().
     */
    public function __construct(
        public string $propertyName,
        public string $className) {}

    public function isRequired(bool $required = true): static
    {
        $this->isRequired = $required;
        return $this;
    }

    public function required(bool $required = true): static
    {
        return $this->isRequired($required);
    }

    public function optional(): static
    {
        return $this->isRequired(false);
    }

    public function requiredState(): bool
    {
        return $this->isRequired;
    }
}