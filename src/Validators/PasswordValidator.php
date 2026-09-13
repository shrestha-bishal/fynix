<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class PasswordValidator extends LengthValidatorBase
{
    protected function __construct(
        string $name, 
        string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->minLength = 8;
        $this->maxLength = 30;
    }

    protected function validateValue(mixed $fieldValue): ?ValidationError
    {
        return $this->validateValueAll($fieldValue)[0] ?? null;
    }

    /** @return list<ValidationError> */
    protected function validateValueAll(mixed $fieldValue): array
    {
        if (!is_string($fieldValue))
            return [new ValidationError($this, "$this->name must be a string.", 'password.invalid')];

        $errors = [];

        if (!preg_match('/[A-Z]/', $fieldValue)) {
            $errors[] = new ValidationError($this, "$this->name must contain at least one uppercase letter.", 'password.uppercase');
        }

        if (!preg_match('/[a-z]/', $fieldValue)) {
            $errors[] = new ValidationError($this, "$this->name must contain at least one lowercase letter.", 'password.lowercase');
        }

        if (!preg_match('/\d/', $fieldValue)) {
            $errors[] = new ValidationError($this, "$this->name must contain at least one number.", 'password.number');
        }

        if (!preg_match('/[\W_]/', $fieldValue)) {
            $errors[] = new ValidationError($this, "$this->name must contain at least one special character.", 'password.special');
        }

        return $errors;
    }
}