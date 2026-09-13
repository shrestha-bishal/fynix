<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

/**
 * Class ObjectArrayValidator
 *
 * Validates a property that contains an array of objects, where each object is validated
 * using the registered rules for its class (e.g. DTOs with ValidationRegistry).
 *
 * Requirements:
 * - The target property must be an array.
 * - Each element in the array must be an object of the specified type.
 * - Validation rules must be registered for the given object class via ValidationRegistry.
 *
 * @package YourNamespace\Validators
 */
class ObjectArrayValidator extends ValidatorBase {
    protected ?int $minItems = null;
    protected ?int $maxItems = null;

    public string $className;

    protected function __construct(string $name, string $propertyName, string $className)
    {
        parent::__construct($name, $propertyName);
        $this->className = $className;
        $this->includeGenericValidation = false;
        $this->supportsBoundValidation = false;
    }

    public function min(int $items): static
    {
        return $this->with('minItems', $this->validateCount($items));
    }

    public function max(int $items): static
    {
        return $this->with('maxItems', $this->validateCount($items));
    }

    protected function validateValue(mixed $fieldValue): ?ValidationError
    {
        return null;
    }

    public function minItems(): ?int
    {
        return $this->minItems;
    }

    public function maxItems(): ?int
    {
        return $this->maxItems;
    }

    private function validateCount(int $items): int
    {
        if ($items < 0) {
            throw new \InvalidArgumentException('Item count constraints must be non-negative.');
        }

        return $items;
    }
}