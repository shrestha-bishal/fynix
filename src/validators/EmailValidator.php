<?php 
namespace PhpValidationCore\Validators;

use PhpValidationCore\ValidationError;
use PhpValidationCore\ValidatorBase;

class EmailValidator extends ValidatorBase 
{
    private bool $_isUsername;

    /**
     * Constructor for the EmailValidation class.
     *
     * @param string $name The name of the validation.
     * @param string $propertyName The name of the field to be validated.
     * @param int $maxLength The maximum length of the number.
     * @param int $minLength The minimum length of the number.
     * @param bool $isRequired Whether the field is required or not. Default is true.
     */
    public function __construct(
        string $name, 
        string $propertyName,
        int $maxLength, 
        int $minLength = 6, 
        bool $isUsername = false,
        bool $isRequired = true)
    {
        $this->_isUsername = $isUsername;
        parent::__construct(
            $name,
            $propertyName,
            $minLength,
            $maxLength,
            "email",
            $isRequired
        );
    }

    public function validate($fieldValue) : ?ValidationError
    {
        $error = null;

        if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL))
            return new ValidationError($this, "$this->name must be a valid email address.");
        
        if (self::validateDNS($fieldValue) === false) // checking the dns records'
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