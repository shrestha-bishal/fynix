<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class PhoneNumberValidator extends LengthValidatorBase
{
    protected function __construct(
        string $name,
        string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->minLength = 10;
        $this->maxLength = 12;
    }

    /** @internal */
    public static function __makeInternal(string $name, string $propertyName): static
    {
        return new static($name, $propertyName);
    }

    public function validate(mixed $fieldValue) : ?ValidationError
    {
        if (!is_string($fieldValue))
            return new ValidationError($this, "$this->name must be a phone number.", 'phone.invalid');

        $sanitisedValue = self::getSanitisedValue($fieldValue);
        $error = null;
        
        if (!is_numeric($sanitisedValue))
            return new ValidationError($this, "$this->name must be a number.");

        if (!self::validatePattern($fieldValue))
            return new ValidationError($this, "$this->name must only contain numbers, spaces, dashes, or plus signs.");
        
        return $error;
    }

    // Check if the phone number contains only valid numeric characters and allowed symbols
    private static function validatePattern(string $fieldValue) : bool
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
    private static function getSanitisedValue(string $fieldValue) : string
    {
        return trim((string) preg_replace('/[^0-9]/', '', $fieldValue)); 
    }
}