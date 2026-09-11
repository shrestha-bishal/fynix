<?php
namespace Fynix\Validators;

use Fynix\ValidationError;

class UsernameValidator extends LengthValidatorBase
{
    /** @var callable(string): bool|null */
    private $existsChecker = null;

    public function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->length(3, 30);
    }

    /**
     * Configure a database or repository-backed uniqueness check.
     * The callback should return true when the username already exists.
     */
    public function uniqueUsing(callable $existsChecker): static
    {
        $this->existsChecker = $existsChecker;
        return $this;
    }

    public function validate(mixed $fieldValue): ?ValidationError
    {
        if (!is_string($fieldValue))
            return new ValidationError($this, "$this->name must be a string.", 'username.invalid');

        if (!preg_match('/^[A-Za-z0-9_]+$/', $fieldValue))
            return new ValidationError($this, "$this->name may only contain letters, numbers, and underscores.", 'username.characters');

        if ($this->existsChecker !== null && ($this->existsChecker)($fieldValue))
            return new ValidationError($this, "$this->name is already taken.", 'username.taken');

        return null;
    }
}