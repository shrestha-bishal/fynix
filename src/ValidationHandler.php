<?php
namespace PhpValidationCore;

use PhpValidationCore\Validators\ObjectArrayValidator;
use PhpValidationCore\Validators\ObjectValidator;

/**
 * Class ValidationHandler
 *
 * Handles the validation process by retrieving validation rules dynamically
 * for any given object instance.
 */

class ValidationHandler {
    /**
     * Validate an instance and return errors.
     *
     * @param object $instance
     * @return array
     */
    public static function validate(object $instance, $flattenErrorToString = true) : array {
        $hash = spl_object_hash($instance);
        if (isset($visited[$hash])) {
            return []; // skip already-validated object
        }

        $visited[$hash] = true;

        $class = get_class($instance);
        $definitions = ValidationRegistry::getRules($class, $instance);
        
        $rules = []; // rules by property

        foreach($definitions as $definition) {
            if($definition instanceof ObjectValidator) {
                $property = $definition->propertyName;
                $nestedInstance = $instance->$property ?? null;

                if ($nestedInstance !== null && is_object($nestedInstance))
                    $rules[$property] = ValidationRegistry::getRules(get_class($nestedInstance), $nestedInstance);

                continue;
            }

            if ($definition instanceof ObjectArrayValidator) {
                $items = $instance->{$definition->propertyName} ?? null;

                if (is_array($items)) {
                    $rules[$definition->propertyName] = [];

                    foreach ($items as $index => $item) {
                        if (is_object($item) && is_a($item, $definition->className)) {
                            $rules[$definition->propertyName][$index] =
                                ValidationRegistry::getRules(get_class($item), $item);
                        }
                    }
                }

                continue;
            }

            $rules[$definition->propertyName] = $definition;
        }

        return Validator::getValidationErrors($rules, $instance, $flattenErrorToString);
    }
}