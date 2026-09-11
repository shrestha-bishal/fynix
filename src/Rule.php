<?php

namespace Fynix;

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
    public static function string(string $fieldOrClass, ?string $propertyName = null): StringValidator
    {
        return StringValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    public static function number(string $fieldOrClass, ?string $propertyName = null): NumberValidator
    {
        return NumberValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    public static function email(string $fieldOrClass, ?string $propertyName = null): EmailValidator
    {
        return EmailValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    public static function phoneNumber(string $fieldOrClass, ?string $propertyName = null): PhoneNumberValidator
    {
        return PhoneNumberValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    public static function password(string $fieldOrClass, ?string $propertyName = null): PasswordValidator
    {
        return PasswordValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    public static function image(string $fieldOrClass, ?string $propertyName = null): ImageValidator
    {
        return ImageValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    public static function images(string $fieldOrClass, ?string $propertyName = null): ImagesValidator
    {
        return ImagesValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    public static function object(string $fieldOrClass, string $propertyOrTargetClass, ?string $targetClass = null): ObjectValidator
    {
        $field = self::resolveField($fieldOrClass, $targetClass === null ? null : $propertyOrTargetClass);
        $targetClass ??= $propertyOrTargetClass;

        return new ObjectValidator($field, $targetClass);
    }

    public static function objectArray(string $fieldOrClass, string $propertyOrTargetClass, ?string $targetClass = null): ObjectArrayValidator
    {
        $field = self::resolveField($fieldOrClass, $targetClass === null ? null : $propertyOrTargetClass);
        $targetClass ??= $propertyOrTargetClass;

        return new ObjectArrayValidator($field, $targetClass);
    }

    public static function username(string $fieldOrClass, ?string $propertyName = null): UsernameValidator
    {
        return UsernameValidator::make(self::resolveField($fieldOrClass, $propertyName));
    }

    private static function resolveField(string $fieldOrClass, ?string $propertyName): string
    {
        return $propertyName === null
            ? $fieldOrClass
            : nameof($fieldOrClass, $propertyName);
    }
}