<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class RegexValidator extends ValidatorBase
{
    private string $pattern;

    protected function __construct(string $name, string $propertyName, string $pattern)
    {
        parent::__construct($name, $propertyName);
        if (@preg_match($pattern, '') === false) {
            throw new \InvalidArgumentException('The regular expression is invalid.');
        }

        $this->pattern = $pattern;
    }

    public function validate(mixed $fieldValue): ?ValidationError
    {
        return is_string($fieldValue) && preg_match($this->pattern, $fieldValue) === 1
            ? null
            : new ValidationError($this, "$this->name has an invalid format.", 'regex.invalid');
    }
}