<?php
namespace Fynix\Validators;

use Fynix\ValidationError;

abstract class ValidatorBase 
{
    protected string $name;
    protected string $propertyName;
    protected ?int $minLength = null;
    protected ?int $maxLength = null;
    protected bool $isRequired = true;
    protected bool $includeGenericValidation = true;
    
    public function __construct(string $name, string $propertyName)
    {
        $this->name = ucfirst($name);
        $this->propertyName = $propertyName;
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
        $this->includeGenericValidation = $enabled;
        return $this;
    }

    public function withoutGenericValidation(): static
    {
        return $this->genericValidation(false);
    }

    protected function validatedConstraint(int|float $value): int
    {
        if ($value < 0 || $value > PHP_INT_MAX || $value != (int) $value) {
            throw new \InvalidArgumentException('Validation constraints must be non-negative integers.');
        }

        return (int) $value;
    }

    public function validateField(mixed $fieldValue) : ?ValidationError
    {
        return $this->validateFieldAll($fieldValue)[0] ?? null;
    }

    /**
     * Validate a value and return every applicable error.
     *
     * The first-error validateField() method remains available for simple consumers.
     */
    /** @return list<ValidationError> */
    public function validateFieldAll(mixed $fieldValue): array
    {
        /** @var list<ValidationError> $errors */
        $errors = [];

        if($this->includeGenericValidation) 
        {
            if (is_string($fieldValue))
                $fieldValue = trim($fieldValue);

            if ($fieldValue === null || $fieldValue === '') {
                if ($this->isRequired)
                    return [new ValidationError($this, "$this->name is required.", 'required')];

                return [];
            }

            $errors = [...$errors, ...$this->validateHTML($fieldValue)];
            $errors = [...$errors, ...$this->validateLength($fieldValue)];
        }

        $errors = [...$errors, ...$this->validateAll($fieldValue)];

        return $errors;
    }

    /**
     * Abstract method to perform validation on the field.
     * This method should be implemented in child classes to define the specific validation logic.
    * @return ValidationError|null The first validation error, if any.
     */
    abstract public function validate(mixed $fieldValue) : ?ValidationError;

    /** @return list<ValidationError> */
    public function validateAll(mixed $fieldValue): array
    {
        $error = $this->validate($fieldValue);
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
}