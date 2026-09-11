<?php
namespace Fynix;

use Fynix\Validators\ObjectArrayValidator;
use Fynix\Validators\ObjectValidator;

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
    /** @return array<string, mixed> */
    public static function validate(object $instance, bool $flattenErrorToString = true) : array {
        $class = get_class($instance);
        $definitions = ValidationRegistry::getRules($class, $instance);
        
        $rules = []; // rules by property
        $structureErrors = [];

        foreach($definitions as $definition) {
            if($definition instanceof ObjectValidator) {
                $property = $definition->propertyName;
                $nestedInstance = $instance->$property ?? null;

                if ($nestedInstance === null) {
                    if ($definition->requiredState())
                        $structureErrors[$property] = self::structureError($property, "$property is required.", 'required', $flattenErrorToString);
                } elseif (!is_object($nestedInstance) || !is_a($nestedInstance, $definition->className)) {
                    $structureErrors[$property] = self::structureError($property, "$property must be an instance of {$definition->className}.", 'object.invalid', $flattenErrorToString);
                } else {
                    $rules[$property] = ValidationRegistry::getRules(get_class($nestedInstance), $nestedInstance);
                }

                continue;
            }

            if ($definition instanceof ObjectArrayValidator) {
                $items = $instance->{$definition->propertyName} ?? null;

                if ($items === null) {
                    if ($definition->requiredState())
                        $structureErrors[$definition->propertyName] = self::structureError($definition->propertyName, "$definition->propertyName is required.", 'required', $flattenErrorToString);
                } elseif (is_array($items)) {
                    $itemCount = count($items);
                    if ($definition->minItems() !== null && $itemCount < $definition->minItems())
                        $structureErrors[$definition->propertyName] = self::structureError($definition->propertyName, "{$definition->propertyName} must contain at least {$definition->minItems()} items.", 'array.min', $flattenErrorToString);
                    elseif ($definition->maxItems() !== null && $itemCount > $definition->maxItems())
                        $structureErrors[$definition->propertyName] = self::structureError($definition->propertyName, "{$definition->propertyName} can contain at most {$definition->maxItems()} items.", 'array.max', $flattenErrorToString);

                    $rules[$definition->propertyName] = [];

                    foreach ($items as $index => $item) {
                        if (is_object($item) && is_a($item, $definition->className)) {
                            $rules[$definition->propertyName][$index] =
                                ValidationRegistry::getRules(get_class($item), $item);
                        } else {
                            $structureErrors[$definition->propertyName][$index] = self::structureError($definition->propertyName . '.' . $index, 'Invalid item — expected object.', 'object.invalid', $flattenErrorToString);
                        }
                    }
                } else {
                    $structureErrors[$definition->propertyName] = self::structureError($definition->propertyName, "$definition->propertyName must be an array.", 'array.invalid', $flattenErrorToString);
                }

                continue;
            }

            $rules[$definition->propertyName()] = $definition;
        }

        $errors = Validator::getValidationErrors($rules, $instance, $flattenErrorToString);
        return self::mergeErrors($structureErrors, $errors);
    }

    /**
     * @param array<string, mixed> $structureErrors
     * @param array<string, mixed> $validationErrors
     * @return array<string, mixed>
     */
    private static function mergeErrors(array $structureErrors, array $validationErrors): array
    {
        foreach ($validationErrors as $key => $value) {
            if (isset($structureErrors[$key]) && is_array($structureErrors[$key]) && is_array($value)) {
                $structureErrors[$key] = self::mergeErrors($structureErrors[$key], $value);
            } elseif (!isset($structureErrors[$key])) {
                $structureErrors[$key] = $value;
            }
        }

        return $structureErrors;
    }

    private static function structureError(string $field, string $message, string $code, bool $flatten): string|ValidationError
    {
        return $flatten ? $message : ValidationError::forField($field, $message, $code);
    }

    /** @return list<array<string, mixed>> */
    public static function validateMany(object ...$instances): array {
        $errors = [];

        foreach($instances as$instance) {
            $errors[] = self::validate($instance);
        }

        return $errors;
    }

    /**
     * @param array<array-key, object> $instances
     * @return array<array-key, array<string, mixed>>
     */
    public static function validateManyAssoc(array $instances, bool $flattenErrorToString = true) : array {
        $errors = [];

        foreach($instances as $key => $instance) {
            $errors[$key] = self::validate($instance, $flattenErrorToString);
        }

        return $errors;
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
    * @param array<string|int, mixed> $errors The nested validation errors.
     * @param string $parentKey The prefix for keys during recursion (used internally).
    * @return array<string, mixed> A flat array with dot-notated keys and corresponding messages.
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

    /** @return array<string, mixed> */
    public static function validateAndFlatten(object $instance): array {
        $errors = self::validate($instance);
        return self::flattenValidationErrors($errors);
    }
}