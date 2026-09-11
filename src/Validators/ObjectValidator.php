<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

/**
 * Class ObjectValidator
 *
 * Validates a nested object (e.g., a DTO) by resolving and invoking its registered validation logic.
 */
class ObjectValidator extends ValidatorBase {

    public string $className;

    protected function __construct(string $name, string $propertyName, string $className)
    {
        parent::__construct($name, $propertyName);
        $this->className = $className;
        $this->includeGenericValidation = false;
    }

    public function validate(mixed $fieldValue): ?ValidationError
    {
        return null;
    }
}