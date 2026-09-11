<?php

namespace Fynix\Rules;

use Fynix\Validators\EmailValidator;
use Fynix\Validators\ImageValidator;
use Fynix\Validators\ImagesValidator;
use Fynix\Validators\LengthValidatorBase;
use Fynix\Validators\NumberValidator;
use Fynix\Validators\ObjectArrayValidator;
use Fynix\Validators\ObjectValidator;
use Fynix\Validators\PasswordValidator;
use Fynix\Validators\PhoneNumberValidator;
use Fynix\Validators\StringValidator;
use Fynix\Validators\UsernameValidator;
use Fynix\Validators\ValidatorBase;
use InvalidArgumentException;

final class RuleBuilder
{
    /** @var class-string */
    private string $className;

    /** @var list<ValidatorBase|ObjectValidator|ObjectArrayValidator> */
    private array $rules = [];

    private ValidatorBase|ObjectValidator|ObjectArrayValidator|null $current = null;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        if (!class_exists($className))
            throw new InvalidArgumentException("Class $className does not exist.");

        $this->className = $className;
    }

    public function string(string $propertyName): static
    {
        return $this->add(new StringValidator($this->label($propertyName), $this->property($propertyName)));
    }

    public function number(string $propertyName): static
    {
        return $this->add(new NumberValidator($this->label($propertyName), $this->property($propertyName)));
    }

    public function email(string $propertyName): static
    {
        return $this->add(new EmailValidator($this->label($propertyName), $this->property($propertyName)));
    }

    public function username(string $propertyName): static
    {
        return $this->add(new UsernameValidator($this->label($propertyName), $this->property($propertyName)));
    }

    public function phone(string $propertyName): static
    {
        return $this->add(new PhoneNumberValidator($this->label($propertyName), $this->property($propertyName)));
    }

    public function password(string $propertyName): static
    {
        return $this->add(new PasswordValidator($this->label($propertyName), $this->property($propertyName)));
    }

    public function image(string $propertyName): static
    {
        return $this->add(new ImageValidator($this->label($propertyName), $this->property($propertyName)));
    }

    public function images(string $propertyName): static
    {
        return $this->add(new ImagesValidator($this->label($propertyName), $this->property($propertyName)));
    }

    /** @param class-string $className */
    public function object(string $propertyName, string $className): static
    {
        return $this->add(new ObjectValidator($this->property($propertyName), $className));
    }

    /** @param class-string $className */
    public function objectArray(string $propertyName, string $className): static
    {
        return $this->add(new ObjectArrayValidator($this->property($propertyName), $className));
    }

    public function min(int|float $value): static
    {
        return $this->configure(static fn($rule) => $rule->min($value));
    }

    public function max(int|float $value): static
    {
        return $this->configure(static fn($rule) => $rule->max($value));
    }

    public function length(int $min, int $max): static
    {
        return $this->configure(static fn($rule) => $rule->length($min, $max));
    }

    public function isRequired(bool $required = true): static
    {
        return $this->configure(static fn($rule) => $rule->isRequired($required));
    }

    public function required(): static
    {
        return $this->isRequired(true);
    }

    public function optional(): static
    {
        return $this->isRequired(false);
    }

    public function verifyDomain(bool $enabled = true): static
    {
        return $this->configure(static fn($rule) => $rule instanceof EmailValidator
            ? $rule->verifyDomain($enabled)
            : throw new InvalidArgumentException('verifyDomain() can only be used after email().'));
    }

    public function uniqueUsing(callable $checker): static
    {
        return $this->configure(static fn($rule) => $rule instanceof UsernameValidator
            ? $rule->uniqueUsing($checker)
            : throw new InvalidArgumentException('uniqueUsing() can only be used after username().'));
    }

    public function maxFileSizeMB(int $megabytes): static
    {
        return $this->configure(static fn($rule) => $rule instanceof ImageValidator || $rule instanceof ImagesValidator
            ? $rule->maxFileSizeMB($megabytes)
            : throw new InvalidArgumentException('maxFileSizeMB() can only be used after image() or images().'));
    }

    /** @return list<ValidatorBase|ObjectValidator|ObjectArrayValidator> */
    public function rules(): array
    {
        return $this->rules;
    }

    /** @return list<ValidatorBase|ObjectValidator|ObjectArrayValidator> */
    public function __invoke(object $instance): array
    {
        if (!$instance instanceof $this->className)
            throw new InvalidArgumentException("Expected instance of {$this->className}.");

        return $this->rules();
    }

    private function add(ValidatorBase|ObjectValidator|ObjectArrayValidator $rule): static
    {
        $this->rules[] = $rule;
        $this->current = $rule;
        return $this;
    }

    /** @param callable(mixed): mixed $callback */
    private function configure(callable $callback): static
    {
        if ($this->current === null)
            throw new InvalidArgumentException('Configure a validator before applying constraints.');

        $callback($this->current);
        return $this;
    }

    private function property(string $propertyName): string
    {
        return \Fynix\nameof($this->className, $propertyName);
    }

    private function label(string $propertyName): string
    {
        return ucfirst(preg_replace('/(?<!^)[A-Z]/', ' $0', $propertyName) ?? $propertyName);
    }
}