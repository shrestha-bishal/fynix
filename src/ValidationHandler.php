<?php
namespace ValidatePhpCore;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * Class ValidationHandler
 *
 * Handles the validation process by retrieving validation rules dynamically
 * for any given object instance.
 */

class ValidationHandler {
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
     * Validate an instance and return errors.
     *
     * @param object $instance
     * @return array
     */
    public static function validate(object $instance) : array {
        $class = get_class($instance);

        //Checking attribute overrides
        $reflection = new ReflectionClass($instance);
        $attributes = $reflection->getAttributes(ValidateWith::class);

        if(!isset(self::$registry[$class])) {
            throw new InvalidArgumentException("No validation rule registered for class $class.");
        }

        $resolver = self::$registry[$class];
        $rules = $resolver($instance);
        
        return Validator::getValidationErrors($rules, $instance);
    }

    public static function clearCache() : void {
        self::$registry = [];
    }
}