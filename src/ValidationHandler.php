<?php
namespace ValidatePhpCore;
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
    public static function validate(object $instance) : array {
        $class = get_class($instance);

        $resolver = ValidationRegistry::getResolver($class);
        $rules = $resolver($instance);
        
        return Validator::getValidationErrors($rules, $instance);
    }
}