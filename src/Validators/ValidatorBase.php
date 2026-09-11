<?php
namespace Fynix\Validators;

use Fynix\ValidationError;

abstract class ValidatorBase 
{
    public string $name;
    public string $propertyName;
    public ?int $minLength = null;
    public ?int $maxLength = null;
    public bool $isRequired = true;
    public $includeGenericValidation = true;
    
    /**
     * Constructor for initializing a validation rule.
     * 
     * @param string $name Name
     * @param string $field_type The type of the field (e.g., 'string', 'number').
     * @param string $field_name The name of the field to validate. (frontend name)
     * @param int $min_length The minimum length of the field value.
     * @param int $max_length The maximum length of the field value.
     * @param string $msg An error message for the validation error.
     * @param bool $is_required (Optional) Whether the field is required. Defaults to true.
     */
    function __construct(string $name, string $propertyName)
    {
        $this->name = ucfirst($name);
        $this->propertyName = $propertyName;
    }

    public function required(bool $required = true): static
    {
        $this->isRequired = $required;
        return $this;
    }

    public function optional(): static
    {
        return $this->required(false);
    }

    public function genericValidation(bool $enabled = true): static
    {
        $this->includeGenericValidation = $enabled;
        return $this;
    }

    public function withoutGenericValidation(): static
    {
        return $this->genericValidation(false);
    }

    protected function validatedConstraint(int|float $value): int
    {
        if ($value < 0 || $value > PHP_INT_MAX || $value != (int) $value) {
            throw new \InvalidArgumentException('Validation constraints must be non-negative integers.');
        }

        return (int) $value;
    }

    public function validateField($fieldValue) : ?ValidationError
    {
        $error = null;

        if($this->includeGenericValidation) 
        {
            if (is_string($fieldValue))
                $fieldValue = trim($fieldValue);

            if ($fieldValue === null || $fieldValue === '') {
                if ($this->isRequired)
                    return new ValidationError($this, "$this->name is required.");

                return null;
            }

            $error = $this->validateHTML($fieldValue);
            if($error !== null) return $error;
            
            $error = $this->validateLength($fieldValue);
            if($error !== null) return $error;
        }

        $error = $this->validate($fieldValue);
        if($error !== null) return $error;

        return $error;
    }

    /**
     * Abstract method to perform validation on the field.
     * This method should be implemented in child classes to define the specific validation logic.
     * @return ValidationError|null An object of ValidationError or null if no errors.
     */
    abstract public function validate($fieldValue) : ?ValidationError;

    /**
     * Validates if the field value is null or empty.
     * @param string $fieldValue The value of the field to validate.
     * @return ValidationError|null An object of ValidationError or null if no errors.
     */
    private function validateNullable($fieldValue) : ?validationError
    {
        if (is_string($fieldValue))
            $fieldValue = trim($fieldValue);

        if(empty($fieldValue) || $fieldValue == null) {
            return new ValidationError($this, "$this->name is required.");
        }

        return null;
    }

    /**
     * Validates if the field value exceeds the maximum length.
     * @param string $fieldValue The value of the field to validate.
     * @return ValidationError|null An object of ValidationError or null if no errors.
     */
    private function validateLength($fieldValue) : ?ValidationError
    {
        if($this->minLength == null || $this->maxLength == null)
            return null;
    
        if (!is_scalar($fieldValue))
            return null;

        $stringLength = strlen((string) $fieldValue);

        if($stringLength < $this->minLength) 
            return new ValidationError($this, "$this->name is too short. This field must be at least $this->minLength characters.");

        if($stringLength > $this->maxLength) 
            return new ValidationError($this, "$this->name is too long. This field can only hold up to $this->maxLength characters.");

        return null;
    }

    /**
     * Validates if the field value is a valid with no HTML tags.
     * @param string $fieldValue The value of the field to validate.
     * @return ValidationError|null An object of ValidationError or null if no errors.
     */
    private function validateHTML($fieldValue) : ?ValidationError
    {
        if(is_string($fieldValue) && preg_match('/<[^>]*>/', $fieldValue))
            return new ValidationError($this, "$this->name cannot contain HTML tags.");
        
        return null;
    }
}