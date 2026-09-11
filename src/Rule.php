<?php
declare(strict_types=1);

namespace Fynix;

use Fynix\Exceptions\UnknownClassException;
use Fynix\Validators\EmailValidator;
use Fynix\Validators\ImageValidator;
use Fynix\Validators\ImagesValidator;
use Fynix\Validators\NumberValidator;
use Fynix\Validators\ObjectArrayValidator;
use Fynix\Validators\ObjectValidator;
use Fynix\Validators\PasswordValidator;
use Fynix\Validators\PhoneNumberValidator;
use Fynix\Validators\StringValidator;
use Fynix\Validators\UsernameValidator;

final class Rule
{
    public static function on(string $ownerClass): ScopedRule
    {
        self::assertClass($ownerClass);

        return new ScopedRule($ownerClass);
    }

    public static function string(string $field): StringValidator
    {
        return StringValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function number(string $field): NumberValidator
    {
        return NumberValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function email(string $field): EmailValidator
    {
        return EmailValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function phoneNumber(string $field): PhoneNumberValidator
    {
        return PhoneNumberValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function password(string $field): PasswordValidator
    {
        return PasswordValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function image(string $field): ImageValidator
    {
        return ImageValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function images(string $field): ImagesValidator
    {
        return ImagesValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function object(string $field, string $targetClass): ObjectValidator
    {
        self::assertClass($targetClass);

        return ObjectValidator::__makeInternal(self::labelFor($field), $field, $targetClass);
    }

    public static function objectArray(string $field, string $targetClass): ObjectArrayValidator
    {
        self::assertClass($targetClass);

        return ObjectArrayValidator::__makeInternal(self::labelFor($field), $field, $targetClass);
    }

    public static function username(string $field): UsernameValidator
    {
        return UsernameValidator::__makeInternal(self::labelFor($field), $field);
    }

    public static function labelFor(string $field): string
    {
        $label = preg_replace('/(?<=[a-z0-9])([A-Z])/', ' $1', str_replace('_', ' ', $field));

        return ucwords($label ?? $field);
    }

    private static function assertClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new UnknownClassException("Class or interface $className does not exist.");
        }
    }
}
