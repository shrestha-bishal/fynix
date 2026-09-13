<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\Contracts\NestedValidator;
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
class ObjectArrayValidator extends ValidatorBase implements NestedValidator {
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

    public function targetClass(): string
    {
        return $this->className;
    }

    public function isCollection(): bool
    {
        return true;
    }

    public function accepts(mixed $value): bool
    {
        return is_array($value);
    }

    public function acceptsItem(mixed $value): bool
    {
        return is_object($value) && is_a($value, $this->className);
    }

    private function validateCount(int $items): int
    {
        if ($items < 0) {
            throw new \InvalidArgumentException('Item count constraints must be non-negative.');
        }

        return $items;
    }
}