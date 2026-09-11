<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class ArrayValidator extends ValidatorBase
{
    protected ?int $minItems = null;
    protected ?int $maxItems = null;

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->includeGenericValidation = false;
    }

    public static function __makeInternal(string $name, string $propertyName): static
    {
        return new static($name, $propertyName);
    }

    public function min(int $items): static
    {
        return $this->with('minItems', $this->validateCount($items));
    }

    public function max(int $items): static
    {
        return $this->with('maxItems', $this->validateCount($items));
    }

    public function validate(mixed $fieldValue): ?ValidationError
    {
        if (($fieldValue === null || $fieldValue === '') && !$this->isRequired) {
            return null;
        }

        if (!is_array($fieldValue)) {
            return new ValidationError($this, "$this->name must be an array.", 'array.invalid');
        }

        $count = count($fieldValue);
        if ($this->minItems !== null && $count < $this->minItems) {
            return new ValidationError($this, "$this->name must contain at least $this->minItems items.", 'array.min');
        }

        if ($this->maxItems !== null && $count > $this->maxItems) {
            return new ValidationError($this, "$this->name can contain at most $this->maxItems items.", 'array.max');
        }

        return null;
    }

    private function validateCount(int $items): int
    {
        if ($items < 0) {
            throw new \InvalidArgumentException('Array count constraints must be non-negative.');
        }

        return $items;
    }
}