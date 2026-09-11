<?php 
namespace Fynix;

use InvalidArgumentException;

class ValidationRegistry {
    /** @var array<class-string, callable> */
    protected static array $registry = [];

    /**
     * Register validation rules for a specific class.
     *
     * @param class-string $className
     * @param callable $resolver
     */
    public static function register(string $className, callable $resolver): void {
        if (!class_exists($className) && !interface_exists($className)) {
            throw new InvalidArgumentException("Class or interface $className does not exist.");
        }

        self::$registry[$className] = $resolver;
    }
    
    /**
     * Get registered validation rule resolver.
     *
     * @param class-string $className
     * @return callable
     */
    public static function getResolver(string $className): callable {
        if (!isset(self::$registry[$className])) {
            throw new InvalidArgumentException("No validation rule registered for class $className.");
        }

        return self::$registry[$className];
    }

    /**
     * @param class-string $className
     * @return list<mixed>|array<string|int, mixed>
     */
    public static function getRules(string $className, object $instance) : array {
        $resolver = ValidationRegistry::getResolver($className);
        $rules = $resolver($instance);
        return $rules;
    }

    public static function clearCache() : void {
        self::$registry = [];
    }
}