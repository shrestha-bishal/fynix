<?php 
namespace Fynix\Validators;

use Fynix\ValidationError;

class NumberValidator extends ValidatorBase {
    private int|float|null $_minNumber = null;
    private int|float|null $_maxNumber = null;

    public function __construct(
        string $name, 
        string $propertyName)
    { 
        parent::__construct($name, $propertyName);
        $this->length(1, 30);
    }

    public function min(int|float $value): static
    {
        if (!is_finite((float) $value)) {
            throw new \InvalidArgumentException('The minimum number must be finite.');
        }

        $this->_minNumber = $value;
        return $this;
    }

    public function max(int|float $value): static
    {
        if (!is_finite((float) $value)) {
            throw new \InvalidArgumentException('The maximum number must be finite.');
        }

        $this->_maxNumber = $value;
        return $this;
    }

    public function length(int $min, int $max): static
    {
        $this->minLength = $this->validatedConstraint($min);
        $this->maxLength = $this->validatedConstraint($max);
        return $this;
    }

    public function validate(mixed $fieldValue) : ?ValidationError
    {
        $error = null;

        if (!is_numeric($fieldValue))
            return new ValidationError($this, "$this->name must be a number.");

        if ($this->_minNumber === null && $this->_maxNumber === null)
            return null;

        if ($this->_minNumber !== null && $fieldValue < $this->_minNumber)
            return new ValidationError($this, "$this->name must be at least $this->_minNumber.");

        if ($this->_maxNumber !== null && $fieldValue > $this->_maxNumber)
            return new ValidationError($this, "$this->name must be at most $this->_maxNumber.");

        return $error;
    }
}