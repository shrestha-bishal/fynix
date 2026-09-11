<?php
declare(strict_types=1);

namespace Fynix;

use InvalidArgumentException;

/**
 * Return a validated property name for a class or object.
 *
 * PHP has no native compile-time nameof() operator. This helper validates the
 * property when validation rules are created, catching spelling mistakes early.
 */
function nameof(object|string $classOrObject, string $propertyName): string
{
    $className = is_object($classOrObject) ? $classOrObject::class : $classOrObject;

    if (!class_exists($className) && !interface_exists($className)) {
        throw new InvalidArgumentException("Class or interface $className does not exist.");
    }

    if (!property_exists($className, $propertyName)) {
        throw new InvalidArgumentException("$className does not contain property $propertyName.");
    }

    return $propertyName;
}