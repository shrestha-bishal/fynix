<?php 
namespace ValidatePhpCore;

use InvalidArgumentException;

class ValidationRegistry {
    /** @var array<class-string, callable> */
    protected static array $registry = [];

    /**
     * Register validation rules for a specific class.
     *
     * @param class-string $className
     * @param callable(object): array $resolver
     */
    public static function register(string $className, callable $resolver) : void {
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

    public static function clearCache() : void {
        self::$registry = [];
    }
}