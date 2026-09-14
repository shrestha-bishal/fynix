<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Closure;
use Fynix\ValidationError;
use Fynix\Contracts\Validatable;

abstract class ValidatorBase implements Validatable
{
    protected string $name;
    protected string $propertyName;
    protected ?int $minLength = null;
    protected ?int $maxLength = null;
    protected bool $isRequired = true;
    protected bool $includeGenericValidation = true;
    /** @var list<mixed>|null */
    protected ?array $allowedValues = null;
    /** @var list<mixed>|null */
    protected ?array $disallowedValues = null;
    protected ?string $sameAsField = null;
    /** @var Closure(object): mixed|null */
    protected ?Closure $sameAsCondition = null;
    protected ?string $differentFromField = null;
    /** @var Closure(object): mixed|null */
    protected ?Closure $differentFromCondition = null;
    protected ?string $requiredIfField = null;
    protected mixed $requiredIfValue = null;
    /** @var Closure(object): bool|null */
    protected ?Closure $requiredIfCondition = null;
    protected bool $hasRequiredIf = false;
    protected bool $requiredIfMatches = true;
    protected ?string $prohibitedIfField = null;
    protected mixed $prohibitedIfValue = null;
    /** @var Closure(object): bool|null */
    protected ?Closure $prohibitedIfCondition = null;
    protected bool $hasProhibitedIf = false;
    protected bool $prohibitedIfMatches = true;
    protected ?object $boundObject = null;
    protected bool $supportsBoundValidation = true;
    /** @var Closure(object): bool|null */
    protected ?Closure $condition = null;
    
    protected function __construct(string $name, string $propertyName)
    {
        $this->name = ucfirst($name);
        $this->propertyName = $propertyName;
    }

    /** @internal */
    public static function __makeInternal(string $name, string $propertyName, mixed ...$arguments): static
    {
        // @phpstan-ignore new.static
        return new static($name, $propertyName, ...$arguments);
    }

    protected function with(string $property, mixed $value): static
    {
        $clone = clone $this;
        $clone->{$property} = $value;

        return $clone;
    }

    public function bindTo(object|string $owner): static
    {
        if (is_string($owner)) {
            return $this;
        }

        return $this->with('boundObject', $owner);
    }

    final public function validate(mixed $fieldValue = null, ?object $data = null): ?ValidationError
    {
        return $this->validateAll($fieldValue, $data)[0] ?? null;
    }

    public function label(string $label): static
    {
        return $this->with('name', $label);
    }

    public function required(bool $required = true): static
    {
        return $this->with('isRequired', $required);
    }

    public function optional(): static
    {
        return $this->required(false);
    }

    public function requiredState(): bool
    {
        return $this->isRequired;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function propertyName(): string
    {
        return $this->propertyName;
    }

    public function minLength(): ?int
    {
        return $this->minLength;
    }

    public function maxLength(): ?int
    {
        return $this->maxLength;
    }

    public function genericValidation(bool $enabled = true): static
    {
        return $this->with('includeGenericValidation', $enabled);
    }

    public function withoutGenericValidation(): static
    {
        return $this->genericValidation(false);
    }

    /**
     * Validate this rule only when the owning object satisfies the condition.
     *
     * @param Closure(object): bool $condition
     */
    public function when(Closure $condition): static
    {
        return $this->with('condition', $condition);
    }

    /** @param list<mixed> $values */
    public function in(array $values): static
    {
        return $this->with('allowedValues', $values);
    }

    /** @param list<mixed> $values */
    public function notIn(array $values): static
    {
        return $this->with('disallowedValues', $values);
    }

    public function sameAs(string|Closure $fieldOrCondition): static
    {
        $clone = $this->with('sameAsField', is_string($fieldOrCondition) ? $fieldOrCondition : null);

        return $clone->with('sameAsCondition', $fieldOrCondition instanceof Closure ? $fieldOrCondition : null);
    }

    public function differentFrom(string|Closure $fieldOrCondition): static
    {
        $clone = $this->with('differentFromField', is_string($fieldOrCondition) ? $fieldOrCondition : null);

        return $clone->with('differentFromCondition', $fieldOrCondition instanceof Closure ? $fieldOrCondition : null);
    }

    public function requiredIf(string|Closure $fieldOrCondition, mixed $value = null): static
    {
        $clone = $this->with('requiredIfField', is_string($fieldOrCondition) ? $fieldOrCondition : null);
        $clone = $clone->with('requiredIfValue', $value);
        $clone = $clone->with('requiredIfCondition', $fieldOrCondition instanceof Closure ? $fieldOrCondition : null);
        $clone = $clone->with('requiredIfMatches', true);

        return $clone->with('hasRequiredIf', true);
    }

    public function requiredUnless(string|Closure $fieldOrCondition, mixed $value = null): static
    {
        $clone = $this->with('requiredIfField', is_string($fieldOrCondition) ? $fieldOrCondition : null);
        $clone = $clone->with('requiredIfValue', $value);
        $clone = $clone->with('requiredIfCondition', $fieldOrCondition instanceof Closure ? $fieldOrCondition : null);
        $clone = $clone->with('requiredIfMatches', false);

        return $clone->with('hasRequiredIf', true);
    }

    public function prohibitedIf(string|Closure $fieldOrCondition, mixed $value = null): static
    {
        $clone = $this->with('prohibitedIfField', is_string($fieldOrCondition) ? $fieldOrCondition : null);
        $clone = $clone->with('prohibitedIfValue', $value);
        $clone = $clone->with('prohibitedIfCondition', $fieldOrCondition instanceof Closure ? $fieldOrCondition : null);
        $clone = $clone->with('prohibitedIfMatches', true);

        return $clone->with('hasProhibitedIf', true);
    }

    public function prohibitedUnless(string|Closure $fieldOrCondition, mixed $value = null): static
    {
        $clone = $this->with('prohibitedIfField', is_string($fieldOrCondition) ? $fieldOrCondition : null);
        $clone = $clone->with('prohibitedIfValue', $value);
        $clone = $clone->with('prohibitedIfCondition', $fieldOrCondition instanceof Closure ? $fieldOrCondition : null);
        $clone = $clone->with('prohibitedIfMatches', false);

        return $clone->with('hasProhibitedIf', true);
    }

    protected function validatedConstraint(int|float $value): int
    {
        if ($value < 0 || $value > PHP_INT_MAX || $value != (int) $value) {
            throw new \InvalidArgumentException('Validation constraints must be non-negative integers.');
        }

        return (int) $value;
    }

    /** @return list<ValidationError> */
    final public function validateAll(mixed $fieldValue = null, ?object $data = null): array
    {
        if ($this->boundObject !== null) {
            if (!$this->supportsBoundValidation) {
                throw new \LogicException('Nested validators must run through ValidationHandler::validate().');
            }

            $fieldValue = $this->boundObject->{$this->propertyName} ?? null;
            $data = $this->boundObject;
        }

        if ($this->condition !== null && ($data === null || !($this->condition)($data))) {
            return [];
        }

        /** @var list<ValidationError> $errors */
        $errors = [];

        $conditionalPresenceError = $this->validateConditionalPresence($fieldValue, $data);
        if ($conditionalPresenceError !== null) {
            return [$conditionalPresenceError];
        }

        if($this->includeGenericValidation) 
        {
            if (is_string($fieldValue))
                $fieldValue = trim($fieldValue);

            if ($fieldValue === null || $fieldValue === '') {
                if ($this->isRequiredFor($data))
                    return [new ValidationError($this, "$this->name is required.", 'required')];

                return [];
            }

            $errors = [...$errors, ...$this->validateHTML($fieldValue)];
            $errors = [...$errors, ...$this->validateLength($fieldValue)];
        }

        $errors = [...$errors, ...$this->validateValueAll($fieldValue)];

        if (empty($errors)) {
            $errors = [...$errors, ...$this->validateValueSet($fieldValue)];
            $errors = [...$errors, ...$this->validateRelatedFields($fieldValue, $data)];
        }

        return $errors;
    }

    /**
     * Abstract method to perform validation on the field.
     * This method should be implemented in child classes to define the specific validation logic.
    * @return ValidationError|null The first validation error, if any.
     */
    abstract protected function validateValue(mixed $fieldValue): ?ValidationError;

    /** @return list<ValidationError> */
    protected function validateValueAll(mixed $fieldValue): array
    {
        $error = $this->validateValue($fieldValue);
        return $error === null ? [] : [$error];
    }

    /**
     * Validates if the field value exceeds the maximum length.
     * @return list<ValidationError> All length errors for the value.
     */
    private function validateLength(mixed $fieldValue) : array
    {
        if($this->minLength == null || $this->maxLength == null)
            return [];

        if (!is_string($fieldValue) && !is_int($fieldValue) && !is_float($fieldValue))
            return [];

        $stringLength = function_exists('mb_strlen')
            ? mb_strlen((string) $fieldValue)
            : strlen((string) $fieldValue);

        if($stringLength < $this->minLength)
            return [new ValidationError($this, "$this->name is too short. This field must be at least $this->minLength characters.", 'length.min', ['min' => $this->minLength])];

        if($stringLength > $this->maxLength)
            return [new ValidationError($this, "$this->name is too long. This field can only hold up to $this->maxLength characters.", 'length.max', ['max' => $this->maxLength])];

        return [];
    }

    /**
     * Validates if the field value is a valid with no HTML tags.
     * @return list<ValidationError> All HTML errors for the value.
     */
    private function validateHTML(mixed $fieldValue) : array
    {
        if(is_string($fieldValue) && preg_match('/<[^>]*>/', $fieldValue) === 1)
            return [new ValidationError($this, "$this->name cannot contain HTML tags.", 'html.forbidden')];
        
        return [];
    }

    /** @return list<ValidationError> */
    private function validateValueSet(mixed $fieldValue): array
    {
        if ($this->allowedValues !== null && !in_array($fieldValue, $this->allowedValues, true)) {
            return [new ValidationError($this, "$this->name must be one of the allowed values.", 'value.not_allowed', ['values' => $this->allowedValues])];
        }

        if ($this->disallowedValues !== null && in_array($fieldValue, $this->disallowedValues, true)) {
            return [new ValidationError($this, "$this->name contains a disallowed value.", 'value.disallowed')];
        }

        return [];
    }

    private function isRequiredFor(?object $data): bool
    {
        if (!$this->hasRequiredIf || $data === null) {
            return $this->isRequired;
        }

        $matches = $this->requiredIfCondition !== null
            ? ($this->requiredIfCondition)($data)
            : (($data->{$this->requiredIfField} ?? null) === $this->requiredIfValue);

        return $this->requiredIfMatches ? $matches : !$matches;
    }

    private function validateConditionalPresence(mixed $fieldValue, ?object $data): ?ValidationError
    {
        if (!$this->hasProhibitedIf || $data === null) {
            return null;
        }

        $matches = $this->prohibitedIfCondition !== null
            ? ($this->prohibitedIfCondition)($data)
            : (($data->{$this->prohibitedIfField} ?? null) === $this->prohibitedIfValue);
        $isProhibited = $this->prohibitedIfMatches ? $matches : !$matches;

        if ($isProhibited && $fieldValue !== null && $fieldValue !== '') {
            return new ValidationError($this, "$this->name is not allowed in the current context.", 'prohibited');
        }

        return null;
    }

    /** @return list<ValidationError> */
    private function validateRelatedFields(mixed $fieldValue, ?object $data): array
    {
        if ($data === null) {
            return [];
        }

        if ($this->sameAsField !== null || $this->sameAsCondition !== null) {
            $expectedValue = $this->sameAsCondition !== null
                ? ($this->sameAsCondition)($data)
                : ($data->{$this->sameAsField} ?? null);

            if ($fieldValue !== $expectedValue) {
                $comparison = $this->sameAsField ?? 'the related value';
                return [new ValidationError($this, "$this->name must match $comparison.", 'same_as')];
            }
        }

        if ($this->differentFromField !== null || $this->differentFromCondition !== null) {
            $expectedValue = $this->differentFromCondition !== null
                ? ($this->differentFromCondition)($data)
                : ($data->{$this->differentFromField} ?? null);

            if ($fieldValue === $expectedValue) {
                $comparison = $this->differentFromField ?? 'the related value';
                return [new ValidationError($this, "$this->name must differ from $comparison.", 'different_from')];
            }
        }

        return [];
    }
}