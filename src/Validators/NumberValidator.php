<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class NumberValidator extends ValidatorBase {
    protected int|float|null $_minNumber = null;
    protected int|float|null $_maxNumber = null;

    protected function __construct(
        string $name, 
        string $propertyName)
    { 
        parent::__construct($name, $propertyName);
        $this->minLength = 1;
        $this->maxLength = 30;
    }

    public function min(int|float $value): static
    {
        if (!is_finite((float) $value)) {
            throw new \InvalidArgumentException('The minimum number must be finite.');
        }

        return $this->with('_minNumber', $value);
    }

    public function max(int|float $value): static
    {
        if (!is_finite((float) $value)) {
            throw new \InvalidArgumentException('The maximum number must be finite.');
        }

        return $this->with('_maxNumber', $value);
    }

    public function length(int $min, int $max): static
    {
        $clone = $this->with('minLength', $this->validatedConstraint($min));

        return $clone->with('maxLength', $this->validatedConstraint($max));
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