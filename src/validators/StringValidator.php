<?php
namespace PhpValidationCore\Validators;

use PhpValidationCore\ValidationError;
use PhpValidationCore\ValidationOptions\StringValidationOptions;

class StringValidator extends ValidatorBase {
    /**
     * Constructor for the StringValidation class.
     *
     * @param string  $name       The name of the validation.
     * @param string  $propertyName  The name of the field to be validated.
     * @param int     $minLength  The minimum allowed length for the string.
     * @param int     $maxLength  The maximum allowed length for the string.
     * @param bool    $isRequired Whether the field is required. Defaults to true.
     */
    
    public function __construct(
        string $name,
        string $propertyName,
        ?StringValidationOptions $options = null)
    { 
        $options ??= new StringValidationOptions();
        parent::__construct($name, $propertyName, $options);
    }

    public function validate($fieldValue) : ?ValidationError
    {
        $error = null;

        if(!is_string($fieldValue))
            return new ValidationError($this, "$this->name must be a string.");

        return $error;
    }
}