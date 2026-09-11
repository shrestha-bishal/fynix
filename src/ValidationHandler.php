<?php
declare(strict_types=1);
namespace Fynix;

use Fynix\Validators\ObjectArrayValidator;
use Fynix\Validators\ObjectValidator;
use Fynix\Contracts\ValidationListener;
use Fynix\Validators\ValidatorBase;
use InvalidArgumentException;

/**
 * Class ValidationHandler
 *
 * Handles the validation process by retrieving validation rules dynamically
 * for any given object instance.
 */

class ValidationHandler {
    /** @var list<ValidationListener> */
    private static array $listeners = [];

    public static function addListener(ValidationListener $listener): void
    {
        self::$listeners[] = $listener;
    }

    public static function clearListeners(): void
    {
        self::$listeners = [];
    }
    /**
     * Validate an instance and return errors.
     *
     * @param object $instance
     * @return array
     */
    /** @return array<string|int, mixed> */
    public static function validate(object $instance, bool $flattenErrorToString = true) : array {
        foreach (self::$listeners as $listener) {
            $listener->beforeValidate($instance);
        }

        $errors = self::validateInternal($instance, $flattenErrorToString);

        foreach (self::$listeners as $listener) {
            $listener->afterValidate($instance, $errors);
        }

        return $errors;
    }

    /** @return array<string|int, mixed> */
    private static function validateInternal(object $instance, bool $flattenErrorToString): array
    {
        $class = get_class($instance);
        $definitions = ValidationRegistry::rulesFor($class);
        
        $rules = []; // rules by property
        $structureErrors = [];

        foreach($definitions as $definition) {
            if($definition instanceof ObjectValidator) {
                $property = $definition->propertyName();
                $nestedInstance = $instance->$property ?? null;

                if ($nestedInstance === null) {
                    if ($definition->requiredState())
                        $structureErrors[$property] = self::structureError($property, "$property is required.", 'required', $flattenErrorToString);
                } elseif (!is_object($nestedInstance) || !is_a($nestedInstance, $definition->className)) {
                    $structureErrors[$property] = self::structureError($property, "$property must be an instance of {$definition->className}.", 'object.invalid', $flattenErrorToString);
                } else {
                    $rules[$property] = ValidationRegistry::rulesFor(get_class($nestedInstance));
                }

                continue;
            }

            if ($definition instanceof ObjectArrayValidator) {
                $property = $definition->propertyName();
                $items = $instance->{$property} ?? null;

                if ($items === null) {
                    if ($definition->requiredState())
                        $structureErrors[$property] = self::structureError($property, "$property is required.", 'required', $flattenErrorToString);
                } elseif (is_array($items)) {
                    $itemCount = count($items);
                    if ($definition->minItems() !== null && $itemCount < $definition->minItems())
                        $structureErrors[$property] = self::structureError($property, "$property must contain at least {$definition->minItems()} items.", 'array.min', $flattenErrorToString);
                    elseif ($definition->maxItems() !== null && $itemCount > $definition->maxItems())
                        $structureErrors[$property] = self::structureError($property, "$property can contain at most {$definition->maxItems()} items.", 'array.max', $flattenErrorToString);

                    $rules[$property] = [];

                    foreach ($items as $index => $item) {
                        if (is_object($item) && is_a($item, $definition->className)) {
                            $rules[$property][$index] =
                                ValidationRegistry::rulesFor(get_class($item));
                        } else {
                            if (!isset($structureErrors[$property]) || !is_array($structureErrors[$property])) {
                                $structureErrors[$property] = [];
                            }

                            $structureErrors[$property][$index] = self::structureError($property . '.' . $index, 'Invalid item -expected object.', 'object.invalid', $flattenErrorToString);
                        }
                    }
                } else {
                    $structureErrors[$property] = self::structureError($property, "$property must be an array.", 'array.invalid', $flattenErrorToString);
                }

                continue;
            }

            if (!$definition instanceof ValidatorBase) {
                throw new InvalidArgumentException('Registered rules must expose a validator field.');
            }

            $rules[$definition->propertyName()] = $definition;
        }

        $errors = Validator::getValidationErrors($rules, $instance, $flattenErrorToString);
        return self::mergeErrors($structureErrors, $errors);
    }

    /**
     * @param array<array-key, mixed> $structureErrors
     * @param array<array-key, mixed> $validationErrors
     * @return array<array-key, mixed>
     */
    private static function mergeErrors(array $structureErrors, array $validationErrors): array
    {
        foreach ($validationErrors as $key => $value) {
            if (isset($structureErrors[$key]) && is_array($structureErrors[$key]) && is_array($value)) {
                $structureErrors[$key] = self::mergeErrors($structureErrors[$key], $value);
            } elseif (!array_key_exists($key, $structureErrors)) {
                $structureErrors[$key] = $value;
            }
        }

        return $structureErrors;
    }

    private static function structureError(string $field, string $message, string $code, bool $flatten): string|ValidationError
    {
        return $flatten ? $message : ValidationError::forField($field, $message, $code);
    }

    /** @return list<array<string|int, mixed>> */
    public static function validateMany(object ...$instances): array {
        $errors = [];

        foreach($instances as$instance) {
            $errors[] = self::validate($instance);
        }

        return $errors;
    }

    /**
     * @param array<array-key, object> $instances
     * @return array<array-key, array<string|int, mixed>>
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