<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class EnumValidator extends ValidatorBase
{
    public string $enumClass;

    protected function __construct(string $name, string $propertyName, string $enumClass)
    {
        parent::__construct($name, $propertyName);
        $this->enumClass = $enumClass;
    }

    public function validate(mixed $fieldValue): ?ValidationError
    {
        if ($fieldValue instanceof $this->enumClass) {
            return null;
        }

        if (is_subclass_of($this->enumClass, \BackedEnum::class)
            && (is_int($fieldValue) || is_string($fieldValue)) ) {
            if (($this->enumClass)::tryFrom($fieldValue) !== null) {
                return null;
            }
        }

        return new ValidationError($this, "$this->name must be a valid enum value.", 'enum.invalid');
    }
}