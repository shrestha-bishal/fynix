<?php
declare(strict_types=1);

namespace Fynix;

use Fynix\Exceptions\UndeclaredPropertyException;
use InvalidArgumentException;

final class ScopedRule
{
    public function __construct(private string $ownerClass)
    {
    }

    public function string(string $field): Validators\StringValidator
    {
        return Rule::string($this->property($field));
    }

    public function number(string $field): Validators\NumberValidator
    {
        return Rule::number($this->property($field));
    }

    public function email(string $field): Validators\EmailValidator
    {
        return Rule::email($this->property($field));
    }

    public function phoneNumber(string $field): Validators\PhoneNumberValidator
    {
        return Rule::phoneNumber($this->property($field));
    }

    public function password(string $field): Validators\PasswordValidator
    {
        return Rule::password($this->property($field));
    }

    public function image(string $field): Validators\ImageValidator
    {
        return Rule::image($this->property($field));
    }

    public function images(string $field): Validators\ImagesValidator
    {
        return Rule::images($this->property($field));
    }

    public function object(string $field, string $targetClass): Validators\ObjectValidator
    {
        return Rule::object($this->property($field), $targetClass);
    }

    public function objectArray(string $field, string $targetClass): Validators\ObjectArrayValidator
    {
        return Rule::objectArray($this->property($field), $targetClass);
    }

    public function username(string $field): Validators\UsernameValidator
    {
        return Rule::username($this->property($field));
    }

    private function property(string $field): string
    {
        try {
            return nameof($this->ownerClass, $field);
        } catch (InvalidArgumentException $exception) {
            throw new UndeclaredPropertyException($exception->getMessage(), 0, $exception);
        }
    }
}
