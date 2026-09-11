<?php
namespace Fynix\Validators;

use Fynix\ValidationError;

class StringValidator extends LengthValidatorBase {
    public function __construct(
        string $name,
        string $propertyName)
    { 
        parent::__construct($name, $propertyName);
        $this->length(2, 50);
    }

    public function validate(mixed $fieldValue) : ?ValidationError
    {
        $error = null;

        if(!is_string($fieldValue))
            return new ValidationError($this, "$this->name must be a string.");

        return $error;
    }
}