<?php
declare(strict_types=1);

namespace Fynix;

use Closure;
use Fynix\Contracts\Validatable;
use Fynix\Contracts\ValidatorRegistryInterface;
use Fynix\Exceptions\UnknownClassException;
use InvalidArgumentException;

final class ValidationRegistry implements ValidatorRegistryInterface
{
    /** @var array<class-string, array<Validatable>> */
    private static array $registry = [];

    /** @param Closure(RuleSet): array<Validatable> $ruleFactory */
    public static function register(string $class, Closure $ruleFactory): void
    {
        if (!class_exists($class)) {
            throw new UnknownClassException("Class or interface $class does not exist.");
        }

        $rules = $ruleFactory(new RuleSet($class));
        foreach ($rules as $rule) {
            // @phpstan-ignore instanceof.alwaysTrue
            if (!$rule instanceof Validatable) {
                throw new InvalidArgumentException('Rule factories must return only Validatable instances.');
            }
        }

        self::$registry[$class] = $rules;
    }

    /** @return array<Validatable> */
    public static function rulesFor(string $class): array
    {
        if (!isset(self::$registry[$class])) {
            throw new InvalidArgumentException("No validation rule registered for class $class.");
        }

        return self::$registry[$class];
    }

    public static function clear(): void
    {
        self::$registry = [];
    }
}
