<?php
namespace Fynix\Validators;

abstract class LengthValidatorBase extends ValidatorBase
{
    public function min(int|float $value): static
    {
        $this->minLength = $this->validatedConstraint($value);
        return $this;
    }

    public function max(int|float $value): static
    {
        $this->maxLength = $this->validatedConstraint($value);
        return $this;
    }

    public function length(int $min, int $max): static
    {
        $this->minLength = $this->validatedConstraint($min);
        $this->maxLength = $this->validatedConstraint($max);
        return $this;
    }
}