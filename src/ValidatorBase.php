<?php
namespace PhpValidationCore;

use PhpValidationCore\ValidationOptions\ValidationOptions;

abstract class ValidatorBase 
{
    public string $name;
    public string $propertyName;
    public ?int $minLength;
    public ?int $maxLength;
    public $isRequired;
    public $fieldType;
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
    function __construct(
        string $name, 
        string $propertyName, 
        ValidationOptions $options,
        ) 
    {
        $this->name = ucfirst($name);
        $this->propertyName = $propertyName;
        $this->includeGenericValidation = $options->includeGenericValidation;
        $this->fieldType = $options->fieldType;
        $this->isRequired = $options->isRequired;
        $this->minLength = $options->length['min'] ?? $options->length[0] ?? null;
        $this->maxLength = $options->length['max'] ?? $options->length[1] ?? null;
    }

    public function validateField($fieldValue) : ?ValidationError
    {
        $error = null;

        if($this->includeGenericValidation) 
        {
            $fieldValue = trim($fieldValue);

            $error = $this->validateHTML($fieldValue);
            if($error !== null) return $error;

            if($this->isRequired) 
            {
                $error = $this->validateNullable($fieldValue);
                if($error !== null) return $error;
            }
            
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
    
        $stringLength = strlen($fieldValue);

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
        if(preg_match('/<[^>]*>/', $fieldValue)) 
            return new ValidationError($this, "$this->name cannot contain HTML tags.");
        
        return null;
    }
}