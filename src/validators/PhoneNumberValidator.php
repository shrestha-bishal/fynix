<?php 
namespace PhpValidationCore\Validators;

use PhpValidationCore\ValidationError;
use PhpValidationCore\ValidationOptions\PhoneNumberValidationOptions;

class PhoneNumberValidator extends ValidatorBase 
{
    /**
     * Constructor for the PhoneNumberValidation class.
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
        ?PhoneNumberValidationOptions $options = null)
    {
        $options ??= new PhoneNumberValidationOptions();
        parent::__construct($name, $propertyName, $options);
    }

    public function validate($fieldValue) : ?ValidationError
    {
        $sanitisedValue = self::getSanitisedValue($fieldValue);
        $error = null;
        
        if (!is_numeric($sanitisedValue))
            return new ValidationError($this, "$this->name must be a number.");

        if (!self::validatePattern($fieldValue))
            return new ValidationError($this, "$this->name must only contain numbers, spaces, dashes, or plus signs.");
        
        return $error;
    }

    // Check if the phone number contains only valid numeric characters and allowed symbols
    private static function validatePattern($fieldValue) : bool 
    {
        $isValid = false;
        $pattern = '/^[0-9\-\+\s]+$/';
        
        if (preg_match($pattern, $fieldValue))
            $isValid = true;

        return $isValid;
    }

    /**
     * Get the sanitised value of the phone number.
     */
    private static function getSanitisedValue($fieldValue) : string 
    {
        return trim(preg_replace('/[^0-9]/', '', $fieldValue)); 
    }
}