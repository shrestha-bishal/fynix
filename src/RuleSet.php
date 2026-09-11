<?php
declare(strict_types=1);

namespace Fynix;

use BadMethodCallException;

/**
 * @method Validators\StringValidator string(string $field)
 * @method Validators\BooleanValidator boolean(string $field)
 * @method Validators\DateTimeValidator dateTime(string $field)
 * @method Validators\ArrayValidator arrayOf(string $field)
 * @method Validators\ArrayValidator array(string $field)
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

    public function string(string $field): Validators\StringValidator { return Rule::on($this->ownerClass)->string($field); }
    public function boolean(string $field): Validators\BooleanValidator { return Rule::on($this->ownerClass)->boolean($field); }
    public function dateTime(string $field): Validators\DateTimeValidator { return Rule::on($this->ownerClass)->dateTime($field); }
    public function array(string $field): Validators\ArrayValidator { return Rule::on($this->ownerClass)->array($field); }
    public function arrayOf(string $field): Validators\ArrayValidator { return Rule::on($this->ownerClass)->arrayOf($field); }
    public function url(string $field): Validators\UrlValidator { return Rule::on($this->ownerClass)->url($field); }
    public function uuid(string $field): Validators\UuidValidator { return Rule::on($this->ownerClass)->uuid($field); }
    public function integer(string $field): Validators\IntegerValidator { return Rule::on($this->ownerClass)->integer($field); }
    public function decimal(string $field): Validators\DecimalValidator { return Rule::on($this->ownerClass)->decimal($field); }
    public function enum(string $field, string $enumClass): Validators\EnumValidator { return Rule::on($this->ownerClass)->enum($field, $enumClass); }
    public function file(string $field): Validators\FileValidator { return Rule::on($this->ownerClass)->file($field); }
    public function ipAddress(string $field): Validators\IpAddressValidator { return Rule::on($this->ownerClass)->ipAddress($field); }
    public function regex(string $field, string $pattern): Validators\RegexValidator { return Rule::on($this->ownerClass)->regex($field, $pattern); }
    public function number(string $field): Validators\NumberValidator { return Rule::on($this->ownerClass)->number($field); }
    public function email(string $field): Validators\EmailValidator { return Rule::on($this->ownerClass)->email($field); }
    public function phoneNumber(string $field): Validators\PhoneNumberValidator { return Rule::on($this->ownerClass)->phoneNumber($field); }
    public function password(string $field): Validators\PasswordValidator { return Rule::on($this->ownerClass)->password($field); }
    public function image(string $field): Validators\ImageValidator { return Rule::on($this->ownerClass)->image($field); }
    public function images(string $field): Validators\ImagesValidator { return Rule::on($this->ownerClass)->images($field); }
    public function object(string $field, string $targetClass): Validators\ObjectValidator { return Rule::on($this->ownerClass)->object($field, $targetClass); }
    public function objectArray(string $field, string $targetClass): Validators\ObjectArrayValidator { return Rule::on($this->ownerClass)->objectArray($field, $targetClass); }
    public function username(string $field): Validators\UsernameValidator { return Rule::on($this->ownerClass)->username($field); }

    /** @param array<int, mixed> $arguments */
    public function __call(string $method, array $arguments): mixed
    {
        $scopedRule = Rule::on($this->ownerClass);
        if (!method_exists($scopedRule, $method)) {
            throw new BadMethodCallException("Unknown rule method $method.");
        }

        return $scopedRule->{$method}(...$arguments);
    }
}
