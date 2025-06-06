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


    /**
     * Flattens a nested validation error array into a dot-notated flat structure.
     *
     * This is useful when binding validation errors to form fields in templates,
     * allowing for consistent key lookups such as `items.0.lengthCm`.
     *
     * Example input:
     * [
     *     'fromAddress' => [
     *         'postcode' => 'Postcode is required.',
     *         'state' => 'State is required.',
     *     ],
     *     'items' => [
     *         0 => ['description' => 'Description is required.']
     *     ],
     *     'name' => 'Name is required.'
     * ]
     *
     * Result:
     * [
     *     'fromAddress.postcode' => 'Postcode is required.',
     *     'fromAddress.state' => 'State is required.',
     *     'items.0.description' => 'Description is required.'
     *     'name' => 'Name is required.',
     * ]
     *
     * @param array $errors The nested validation errors.
     * @param string $parentKey The prefix for keys during recursion (used internally).
     * @return array A flat array with dot-notated keys and corresponding messages.
     */
    public static function flattenValidationErrors(array $errors, string $parentKey = '') : array 
    {
        $flattened = [];

        foreach($errors as $key => $value) {
            $dotKey = $parentKey === '' ? (string)$key : "{$parentKey}.{$key}";

            if(is_array($value)) {
                $flattened += self::flattenValidationErrors($value, $dotKey);
            }else {
                $flattened[$dotKey] = $value;
            }
        }

        return $flattened;
    }

    public static function validateAndFlatten(object $instance) {
        $errors = self::validate($instance);
        return self::flattenValidationErrors($errors);
    }
}