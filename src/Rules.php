<?php
declare(strict_types=1);

namespace Fynix;

use Fynix\Rules\RuleBuilder;

final class Rules
{
    /**
     * Start a compatibility builder for a DTO class.
     *
     * @param class-string $className
     */
    public static function for(string $className): RuleBuilder
    {
        return new RuleBuilder($className);
    }
}