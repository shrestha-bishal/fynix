<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class EmailValidator extends LengthValidatorBase
{
    protected bool $_verifyDomain = false;

    protected function __construct(
        string $name, 
        string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->minLength = 6;
        $this->maxLength = 100;
    }

    public function verifyDomain(bool $enabled = true): static
    {
        return $this->with('_verifyDomain', $enabled);
    }

    public function validate(mixed $fieldValue) : ?ValidationError
    {
        $error = null;

        if (!is_string($fieldValue) || !filter_var($fieldValue, FILTER_VALIDATE_EMAIL))
            return new ValidationError($this, "$this->name must be a valid email address.", 'email.invalid');
        
        if ($this->_verifyDomain && self::validateDNS($fieldValue) === false)
            return new ValidationError($this, "The domain of the email address is invalid.");

        if (substr_count($fieldValue, '@') > 1) //counting '@'
            return new ValidationError($this, "Email address contains multiple '@' symbols.");
        
        if (self::validateObscuredEmail($fieldValue)) //counting consecutive dots
            return new ValidationError($this, "Email address contains consecutive dots.");

        return $error;
    }

    private static function validateDNS(string $fieldValue) : bool 
    {
        $parts = explode('@', $fieldValue, 2);

        if (count($parts) !== 2 || $parts[1] === '') {
            return false;
        }

        return checkdnsrr($parts[1], "MX") === true;
    }

    private static function validateObscuredEmail(string $fieldValue) : bool
    {
        $isValid = false;
        if (preg_match('/(\.{2,})/', $fieldValue))
            $isValid = true;

        return $isValid;
    }

}