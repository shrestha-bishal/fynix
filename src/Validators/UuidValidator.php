<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class UuidValidator extends ValidatorBase
{
    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

        return is_string($fieldValue) && preg_match($pattern, $fieldValue) === 1
            ? null
            : new ValidationError($this, "$this->name must be a valid UUID.", 'uuid.invalid');
    }
}