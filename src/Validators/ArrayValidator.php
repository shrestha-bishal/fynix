<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class ArrayValidator extends ValidatorBase
{
    protected ?int $minItems = null;
    protected ?int $maxItems = null;
    protected ?ValidatorBase $itemValidator = null;

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->includeGenericValidation = false;
    }

    public function min(int $items): static
    {
        return $this->with('minItems', $this->validateCount($items));
    }

    public function max(int $items): static
    {
        return $this->with('maxItems', $this->validateCount($items));
    }

    public function each(ValidatorBase $validator): static
    {
        return $this->with('itemValidator', $validator);
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        return $this->validateAll($fieldValue)[0] ?? null;
    }

    /** @return list<ValidationError> */
    public function validateAll(mixed $fieldValue): array
    {
        if (($fieldValue === null || $fieldValue === '') && !$this->isRequired) {
            return [];
        }

        if (!is_array($fieldValue)) {
            return [new ValidationError($this, "$this->name must be an array.", 'array.invalid')];
        }

        $count = count($fieldValue);
        if ($this->minItems !== null && $count < $this->minItems) {
            return [new ValidationError($this, "$this->name must contain at least $this->minItems items.", 'array.min')];
        }

        if ($this->maxItems !== null && $count > $this->maxItems) {
            return [new ValidationError($this, "$this->name can contain at most $this->maxItems items.", 'array.max')];
        }

        if ($this->itemValidator === null) {
            return [];
        }

        $errors = [];
        foreach ($fieldValue as $index => $item) {
            foreach ($this->itemValidator->validateFieldAll($item) as $error) {
                $errors[] = ValidationError::forField(
                    $this->propertyName . '.' . $index,
                    $error->message,
                    $error->code,
                    $error->parameters
                );
            }
        }

        return $errors;
    }

    private function validateCount(int $items): int
    {
        if ($items < 0) {
            throw new \InvalidArgumentException('Array count constraints must be non-negative.');
        }

        return $items;
    }
}