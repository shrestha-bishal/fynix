<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\Contracts\NestedValidator;
use Fynix\ValidationError;

/**
 * Class ObjectValidator
 *
 * Validates a nested object (e.g., a DTO) by resolving and invoking its registered validation logic.
 */
class ObjectValidator extends ValidatorBase implements NestedValidator {

    public string $className;

    protected function __construct(string $name, string $propertyName, string $className)
    {
        parent::__construct($name, $propertyName);
        $this->className = $className;
        $this->includeGenericValidation = false;
        $this->supportsBoundValidation = false;
    }

    protected function validateValue(mixed $fieldValue): ?ValidationError
    {
        return null;
    }

    public function targetClass(): string
    {
        return $this->className;
    }

    public function isCollection(): bool
    {
        return false;
    }

    public function accepts(mixed $value): bool
    {
        return is_object($value) && is_a($value, $this->className);
    }

    public function acceptsItem(mixed $value): bool
    {
        return $this->accepts($value);
    }

    public function minItems(): ?int
    {
        return null;
    }

    public function maxItems(): ?int
    {
        return null;
    }
}