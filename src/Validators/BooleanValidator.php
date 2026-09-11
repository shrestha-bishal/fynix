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

    public static function __makeInternal(string $name, string $propertyName): static
    {
        return new static($name, $propertyName);
    }

    public function validate(mixed $fieldValue): ?ValidationError
    {
        return is_bool($fieldValue)
            ? null
            : new ValidationError($this, "$this->name must be a boolean.", 'boolean.invalid');
    }
}