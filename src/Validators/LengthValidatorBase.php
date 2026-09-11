<?php
declare(strict_types=1);

namespace Fynix\Validators;

abstract class LengthValidatorBase extends ValidatorBase
{
    public function min(int|float $value): static
    {
        return $this->with('minLength', $this->validatedConstraint($value));
    }

    public function max(int|float $value): static
    {
        return $this->with('maxLength', $this->validatedConstraint($value));
    }

    public function length(int $min, int $max): static
    {
        $clone = $this->with('minLength', $this->validatedConstraint($min));

        return $clone->with('maxLength', $this->validatedConstraint($max));
    }
}