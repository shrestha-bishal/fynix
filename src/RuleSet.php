<?php
declare(strict_types=1);

namespace Fynix;

use BadMethodCallException;

/**
 * @method Validators\StringValidator string(string $field)
 * @method Validators\BooleanValidator boolean(string $field)
 * @method Validators\DateTimeValidator dateTime(string $field)
 * @method Validators\ArrayValidator arrayOf(string $field)
 * @method Validators\UrlValidator url(string $field)
 * @method Validators\UuidValidator uuid(string $field)
 * @method Validators\IntegerValidator integer(string $field)
 * @method Validators\DecimalValidator decimal(string $field)
 * @method Validators\EnumValidator enum(string $field, string $enumClass)
 * @method Validators\FileValidator file(string $field)
 * @method Validators\IpAddressValidator ipAddress(string $field)
 * @method Validators\RegexValidator regex(string $field, string $pattern)
 * @method Validators\NumberValidator number(string $field)
 * @method Validators\EmailValidator email(string $field)
 * @method Validators\PhoneNumberValidator phoneNumber(string $field)
 * @method Validators\PasswordValidator password(string $field)
 * @method Validators\ImageValidator image(string $field)
 * @method Validators\ImagesValidator images(string $field)
 * @method Validators\ObjectValidator object(string $field, string $targetClass)
 * @method Validators\ObjectArrayValidator objectArray(string $field, string $targetClass)
 * @method Validators\UsernameValidator username(string $field)
 */
final class RuleSet
{
    public function __construct(private string $ownerClass)
    {
    }

    public function __call(string $method, array $arguments): mixed
    {
        $scopedRule = Rule::on($this->ownerClass);
        if (!method_exists($scopedRule, $method)) {
            throw new BadMethodCallException("Unknown rule method $method.");
        }

        return $scopedRule->{$method}(...$arguments);
    }
}
