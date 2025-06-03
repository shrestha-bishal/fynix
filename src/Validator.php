<?php 
namespace PhpValidationCore;

/**
 * Class Validator
 *
 * This class serves as a core utility for validating various data types and input fields. 
 * @package PhpValidationCore
 * @category Validation
 * @version 1.0.0
 * @author Bishal Shrestha <bishal.shrestha@outlook.com.au>
 * @license MIT License
 * @copyright Copyright (c) 2025, Bishal Shrestha. All rights reserved.
 * @see packagist link
 * @link https://github.com/shrestha-bishal
 */

class Validator
{
    /**
     * Retrieve validation errors for the given data object and rules.
     *
     * @param array  $rules             An array of validation rule instances.
     * @param object $data              The data object to validate.
     * @param bool   $flattenToString  If true, returns error messages as strings; otherwise, returns ValidationError objects.
     *
     * @return array<string, string[]|ValidationError[]> An associative array of validation errors keyed by property name.
     */
    public static function getValidationErrors(array $rules, object $data, bool $flattenToString = true) : array {
      $errors = [];

      foreach($rules as $rule) 
      {
        $fieldValue = isset($data->{$rule->propertyName}) ? $data->{$rule->propertyName} : null;
        $validation = $rule->validateField($fieldValue);
      
        if($validation != null) {
          $errors[$rule->propertyName] = $flattenToString ? $validation->message : $validation;
        }
      }

      return $errors;
    }  
}