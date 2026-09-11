<?php
namespace Fynix\Validators;

use Fynix\ValidationError;

class StringValidator extends LengthValidatorBase {
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
        string $propertyName)
    { 
        parent::__construct($name, $propertyName);
        $this->length(2, 50);
    }

    public function validate($fieldValue) : ?ValidationError
    {
        $error = null;

        if(!is_string($fieldValue))
            return new ValidationError($this, "$this->name must be a string.");

        return $error;
    }
}