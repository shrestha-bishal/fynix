<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class IpAddressValidator extends ValidatorBase
{
    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        return is_string($fieldValue) && filter_var($fieldValue, FILTER_VALIDATE_IP) !== false
            ? null
            : new ValidationError($this, "$this->name must be a valid IP address.", 'ip.invalid');
    }
}