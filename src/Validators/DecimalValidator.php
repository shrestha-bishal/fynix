<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class DecimalValidator extends ValidatorBase
{
    protected ?float $minValue = null;
    protected ?float $maxValue = null;

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
    }

    public function min(float|int $value): static
    {
        return $this->with('minValue', (float) $value);
    }

    public function max(float|int $value): static
    {
        return $this->with('maxValue', (float) $value);
    }

    protected function validateValue(mixed $fieldValue): ?ValidationError
    {
        if (!is_float($fieldValue)) {
            return new ValidationError($this, "$this->name must be a decimal number.", 'decimal.invalid');
        }

        if ($this->minValue !== null && $fieldValue < $this->minValue) {
            return new ValidationError($this, "$this->name must be at least $this->minValue.", 'decimal.min');
        }

        if ($this->maxValue !== null && $fieldValue > $this->maxValue) {
            return new ValidationError($this, "$this->name must be at most $this->maxValue.", 'decimal.max');
        }

        return null;
    }
}