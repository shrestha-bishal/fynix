<?php 
namespace Fynix\Validators;

use Fynix\ValidationError;

class PasswordValidator extends LengthValidatorBase
{
    public function __construct(
        string $name, 
        string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->length(8, 30);
    }

    public function validate(mixed $fieldValue) : ?ValidationError
    {
        return $this->validateAll($fieldValue)[0] ?? null;
    }

    /** @return list<ValidationError> */
    public function validateAll(mixed $fieldValue): array
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