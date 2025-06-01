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
    protected static ?array $methodMappings = null;

    protected static function buildMethodMappings(): void {
        self:: $methodMappings = [];

        $reflection = new ReflectionClass(ValidationRules::class);
        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
            if(!str_starts_with($method->name, 'validate'))
                continue;

            $params = $method->getParameters();
            if(count($params) !== 1)
                continue;
            
            $param = $params[0];
            $type = $param->getType();

            if($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();
                self::$methodMappings[$className] = $method->name;
            }
        }
    }
 
    public static function validate(object $instance) : array {
        $class = get_class($instance);

        //Checking attribute overrides
        $reflection = new ReflectionClass($instance);
        $attributes = $reflection->getAttributes(ValidateWith::class);

        if(!empty($attributes)) {
            /** @var ValidateWith $attr */
            $attr = $attributes[0]->newInstance();
            $method = $attr->methodName;

            if(!method_exists(ValidationRules::class, $method)) {
                throw new InvalidArgumentException("Validation rule '$method' not found in ValidationRules.");
            }
         
            return ValidationRules::$method($instance);
        }

        // build method on demand
        if(self::$methodMappings === null) {
            self::buildMethodMappings();
        }

        // Checking if there is a mapped validation rule method for the class
        if(!isset(self::$methodMappings[$class])) {
            throw new InvalidArgumentException("No validation rule found for class $class.");
        }

        $method = self::$methodMappings[$class];
        $rules =  ValidationRules::$method($instance);
        return Validator::getValidationErrors($rules, $instance);
    }

    public static function clearCache() : void {
        self::$methodMappings = null;
    }
}