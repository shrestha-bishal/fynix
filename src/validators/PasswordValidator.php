<?php 
namespace PhpValidationCore\Validators;

use ValidatePhpCore\ValidationError;
use ValidatePhpCore\ValidatorBase;

class PasswordValidator extends ValidatorBase 
{
    /**
     * Constructor for the PhoneNumberValidation class.
     *
     * @param string $name The name of the validation.
     * @param string $fieldName The name of the field to be validated.
     * @param int $maxLength The maximum length of the number.
     * @param int $minLength The minimum length of the number.
     * @param bool $isRequired Whether the field is required or not. Default is true.
     */
    public function __construct(
        string $name, 
        string $fieldName, 
        int $maxLength = 30,
        int $minLength = 8,
        bool $isRequired = true)
    {
        parent::__construct(
            $name, 
            $fieldName, 
            $minLength,
            $maxLength,
            "password",
            $isRequired
        );
    }

    public function validate($fieldValue) : ?ValidationError
    {
        $error = null;

        if (!preg_match('/[A-Z]/', $fieldValue)) {
            return new ValidationError($this, "$this->name must contain at least one uppercase letter.");
        }

        if (!preg_match('/[a-z]/', $fieldValue)) {
            return new ValidationError($this, "$this->name must contain at least one lowercase letter.");
        }

        if (!preg_match('/\d/', $fieldValue)) {
            return new ValidationError($this, "$this->name must contain at least one number.");
        }

        if (!preg_match('/[\W_]/', $fieldValue)) {
            return new ValidationError($this, "$this->name must contain at least one special character.");
        }

        // if (preg_match('/\s/', $fieldValue)) {
        //     return new ValidationError($this, "$this->name cannot contain spaces.");
        // }

        return $error;
    }
}