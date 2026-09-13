<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class StringValidator extends LengthValidatorBase {
    protected function __construct(
        string $name,
        string $propertyName)
    { 
        parent::__construct($name, $propertyName);
        $this->minLength = 2;
        $this->maxLength = 50;
    }

    public function validate(mixed $fieldValue = null) : ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        $error = null;

        if(!is_string($fieldValue))
            return new ValidationError($this, "$this->name must be a string.");

        return $error;
    }
}