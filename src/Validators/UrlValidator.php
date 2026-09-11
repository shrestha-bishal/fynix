<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class UrlValidator extends LengthValidatorBase
{
    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->minLength = 1;
        $this->maxLength = 2048;
    }

    public function validate(mixed $fieldValue): ?ValidationError
    {
        return is_string($fieldValue) && filter_var($fieldValue, FILTER_VALIDATE_URL) !== false
            ? null
            : new ValidationError($this, "$this->name must be a valid URL.", 'url.invalid');
    }
}