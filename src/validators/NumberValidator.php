<?php 
namespace ValidatePhpCore\Validators;

use ValidatePhpCore\ValidationError;
use ValidatePhpCore\ValidatorBase;

class NumberValidator extends ValidatorBase {
    private int $_minNumber;
    private int $_maxNumber;

    /**
     * Constructor for the NumberValidation class.
     *
     * @param string $name The name of the validation.
     * @param string $fieldName The name of the field to be validated.
     * @param int $maxLength The maximum length of the number.
     * @param int $minNumber The minimum value of the number.
     * @param int $maxNumber The maximum value of the number.
     * @param int $minLength The minimum value of the number.
     * @param bool $isRequired Whether the field is required or not. Default is true.
     */
    
    public function __construct(
        string $name, 
        string $fieldName, 
        int $maxLength,
        int $minNumber, 
        int $maxNumber, 
        int $minLength = 1,
        bool $isRequired = true)
    {
        $this->_minNumber = $minNumber;
        $this->_maxNumber = $maxNumber;

        parent::__construct(
            $name,
            $fieldName, 
            $minLength,
            $maxLength,
            "number",
            $isRequired
        );
    }

    public function validate($fieldValue) : ?ValidationError
    {
        $error = null;

        if (!is_numeric($fieldValue))
            return new ValidationError($this, "$this->name must be a number.");

        if ($fieldValue < $this->_minNumber || $fieldValue > $this->_maxNumber)
            return new ValidationError($this, "$this->name must be between $this->_minNumber and $this->_maxNumber.");

        return $error;
    }
}

