<?php 
namespace Fynix\Validators;

use Fynix\ValidationError;

class EmailValidator extends LengthValidatorBase
{
    private bool $_isUsername = false;
    private bool $_verifyDomain = false;

    public function __construct(
        string $name, 
        string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->length(6, 100);
    }

    public function username(bool $enabled = true): static
    {
        $this->_isUsername = $enabled;
        return $this;
    }

    public function verifyDomain(bool $enabled = true): static
    {
        $this->_verifyDomain = $enabled;
        return $this;
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

        if($this->_isUsername) 
        {
            $isExistingUsername = self::validateExitingUsername($fieldValue);
            if($isExistingUsername) 
            {
                return new ValidationError($this, "Username already exists. Please choose a different one.");
            }
        }

        return $error;
    }

    private static function validateDNS(string $fieldValue) : bool 
    {
        $isValid = false;
        $domain = substr(strrchr($fieldValue, '@'), 1); // Extracting the domain from the email
        
        if(checkdnsrr($domain, "MX")) // Mail exchange records
            $isValid = true;

        return $isValid;
    }

    private static function validateObscuredEmail(string $fieldValue) : bool
    {
        $isValid = false;
        if (preg_match('/(\.{2,})/', $fieldValue))
            $isValid = true;

        return $isValid;
    }

    private static function validateExitingUsername(string $fieldValue) : bool 
    {
        // Check the database records to see if the username exists.
        return false;
    }
}