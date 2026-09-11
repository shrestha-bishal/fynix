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

    public function boolean(string $field): Validators\BooleanValidator
    {
        return Rule::boolean($this->property($field));
    }

    public function dateTime(string $field): Validators\DateTimeValidator
    {
        return Rule::dateTime($this->property($field));
    }

    public function arrayOf(string $field): Validators\ArrayValidator
    {
        return Rule::arrayOf($this->property($field));
    }

    public function array(string $field): Validators\ArrayValidator
    {
        return $this->arrayOf($field);
    }

    public function url(string $field): Validators\UrlValidator
    {
        return Rule::url($this->property($field));
    }

    public function uuid(string $field): Validators\UuidValidator
    {
        return Rule::uuid($this->property($field));
    }

    public function integer(string $field): Validators\IntegerValidator
    {
        return Rule::integer($this->property($field));
    }

    public function decimal(string $field): Validators\DecimalValidator
    {
        return Rule::decimal($this->property($field));
    }

    public function enum(string $field, string $enumClass): Validators\EnumValidator
    {
        return Rule::enum($this->property($field), $enumClass);
    }

    public function file(string $field): Validators\FileValidator
    {
        return Rule::file($this->property($field));
    }

    public function ipAddress(string $field): Validators\IpAddressValidator
    {
        return Rule::ipAddress($this->property($field));
    }

    public function regex(string $field, string $pattern): Validators\RegexValidator
    {
        return Rule::regex($this->property($field), $pattern);
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
