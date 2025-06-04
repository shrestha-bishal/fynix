<?php 
namespace PhpValidationCore\Validators;

use PhpValidationCore\ValidationError;
use PhpValidationCore\ValidationOptions\NumberValidationOptions;

class NumberValidator extends ValidatorBase {
    private ?int $_minNumber;
    private ?int $_maxNumber;

    /**
     * Constructor for the NumberValidation class.
     *
     * @param string $name The name of the validation.
     * @param string $propertyName The name of the field to be validated.
     * @param int $maxLength The maximum length of the number.
     * @param int $minNumber The minimum value of the number.
     * @param int $maxNumber The maximum value of the number.
     * @param int $minLength The minimum value of the number.
     * @param bool $isRequired Whether the field is required or not. Default is true.
     */
    
    public function __construct(
        string $name, 
        string $propertyName, 
        ?NumberValidationOptions $options = null)
    { 
        $options ??= new NumberValidationOptions();

        if($options->number != null) {
            $this->_minNumber = $options->number['min'] ?? $options->number[0] ?? null;
            $this->_maxNumber = $options->number['max'] ?? $options->number[1] ?? null;
        }

        parent::__construct($name, $propertyName, $options);
    }

    public function validate($fieldValue) : ?ValidationError
    {
        $error = null;

        if (!is_numeric($fieldValue))
            return new ValidationError($this, "$this->name must be a number.");

        if ($this->_minNumber == null || $fieldValue > $this->_maxNumber == null)
            return null;

        if ($fieldValue < $this->_minNumber || $fieldValue > $this->_maxNumber)
            return new ValidationError($this, "$this->name must be between $this->_minNumber and $this->_maxNumber.");

        return $error;
    }
}