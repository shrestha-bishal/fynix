<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class UsernameValidator extends LengthValidatorBase
{
    /** @var callable(string): bool|null */
    protected $existsChecker = null;

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->minLength = 3;
        $this->maxLength = 30;
    }

    /**
     * Configure a database or repository-backed uniqueness check.
     * The callback should return true when the username already exists.
     */
    public function uniqueUsing(callable $existsChecker): static
    {
        return $this->with('existsChecker', $existsChecker);
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        if (!is_string($fieldValue))
            return new ValidationError($this, "$this->name must be a string.", 'username.invalid');

        if (!preg_match('/^[A-Za-z0-9_]+$/', $fieldValue))
            return new ValidationError($this, "$this->name may only contain letters, numbers, and underscores.", 'username.characters');

        if ($this->existsChecker !== null && ($this->existsChecker)($fieldValue))
            return new ValidationError($this, "$this->name is already taken.", 'username.taken');

        return null;
    }
}