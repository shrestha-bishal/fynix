<?php
declare(strict_types=1);

namespace Fynix\Contracts;

use Closure;
use Fynix\RuleSet;

interface ValidatorRegistryInterface
{
    /** @param Closure(RuleSet): array<Validatable> $ruleFactory */
    public static function register(string $class, Closure $ruleFactory): void;

    /** @return array<Validatable> */
    public static function rulesFor(string $class): array;
}
