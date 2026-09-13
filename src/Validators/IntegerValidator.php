<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class IntegerValidator extends ValidatorBase
{
    protected ?int $minValue = null;
    protected ?int $maxValue = null;

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
    }

    public function min(int $value): static
    {
        return $this->with('minValue', $value);
    }

    public function max(int $value): static
    {
        return $this->with('maxValue', $value);
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        if (!is_int($fieldValue)) {
            return new ValidationError($this, "$this->name must be an integer.", 'integer.invalid');
        }

        if ($this->minValue !== null && $fieldValue < $this->minValue) {
            return new ValidationError($this, "$this->name must be at least $this->minValue.", 'integer.min');
        }

        if ($this->maxValue !== null && $fieldValue > $this->maxValue) {
            return new ValidationError($this, "$this->name must be at most $this->maxValue.", 'integer.max');
        }

        return null;
    }
}