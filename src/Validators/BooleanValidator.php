<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class BooleanValidator extends ValidatorBase
{
    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        return is_bool($fieldValue)
            ? null
            : new ValidationError($this, "$this->name must be a boolean.", 'boolean.invalid');
    }
}