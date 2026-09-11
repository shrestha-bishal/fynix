<?php

namespace Fynix;

use Fynix\Rules\RuleBuilder;

final class Rules
{
    /**
     * Start a fluent rule definition for a DTO class.
     *
     * @param class-string $className
     */
    public static function for(string $className): RuleBuilder
    {
        return new RuleBuilder($className);
    }
}