<?php 
namespace Fynix\Validators;

/**
 * Class ObjectArrayValidator
 *
 * Validates a property that contains an array of objects, where each object is validated
 * using the registered rules for its class (e.g. DTOs with ValidationRegistry).
 *
 * Usage:
 * ```
 * new ObjectArrayValidator('items', FreightItemDto::class)
 * ```
 *
 * Requirements:
 * - The target property must be an array.
 * - Each element in the array must be an object of the specified type.
 * - Validation rules must be registered for the given object class via ValidationRegistry.
 *
 * @package YourNamespace\Validators
 */
class ObjectArrayValidator {
    private ?int $minItems = null;
    private ?int $maxItems = null;
    private bool $isRequired = true;

    public function __construct(
        public string $propertyName,
        public string $className) {}

    public function min(int $items): static
    {
        $this->minItems = $this->validateCount($items);
        return $this;
    }

    public function max(int $items): static
    {
        $this->maxItems = $this->validateCount($items);
        return $this;
    }

    public function isRequired(bool $required = true): static
    {
        $this->isRequired = $required;
        return $this;
    }

    public function required(bool $required = true): static
    {
        return $this->isRequired($required);
    }

    public function optional(): static
    {
        return $this->isRequired(false);
    }

    public function requiredState(): bool
    {
        return $this->isRequired;
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