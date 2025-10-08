<?php 
namespace Fynix;

use Fynix\Validators\ValidatorBase;

/**
 * Class Validator
 *
 * This class serves as a core utility for validating various data types and input fields. 
 * @package Fynix
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
     * @param ValidatorBase[] $rules   An array of validation rule instances
     * @param object $data              The data object to validate.
     * @param bool   $flattenToString  If true, returns error messages as strings; otherwise, returns ValidationError objects.
     *
     * @return array<string, string[]|ValidationError[]> An associative array of validation errors keyed by property name.
     */
    public static function getValidationErrors(array $rules, object $data, bool $flattenToString = true) : array {
      $errors = [];

      foreach($rules as $key => $rule) 
      {
        $propertyValue = $data->{$key} ?? null;
        
        if(is_array($rule)) 
        {
          // case: array of objects
          if (is_array($propertyValue)) 
          {
              foreach ($rule as $index => $nestedRules) {
                $nestedItem = $propertyValue[$index];

                if (!is_object($nestedItem)) {
                    $errors[$key][$index] = 'Invalid item — expected object.';
                    continue;
                }

                $nestedErrors = self::getValidationErrors($nestedRules, $nestedItem, $flattenToString);
                if (!empty($nestedErrors)) {
                    $errors[$key][$index] = $nestedErrors;
                }
              }
          }
          
          // case: single nested object
          else if (is_object($propertyValue)) 
          {
            $nestedErrors = self::getValidationErrors($rule, $propertyValue, $flattenToString);
            
            if(!empty($nestedErrors))
              $errors[$key] = $nestedErrors;
          }

          // else: not valid structure
          else {
              $errors[$key] = 'Invalid value — expected object or array of objects.';
          }

          continue;
        }
        
        if($rule instanceof ValidatorBase) {
          $fieldValue = isset($data->{$rule->propertyName}) ? $data->{$rule->propertyName} : null;
          $validation = $rule->validateField($fieldValue);
          
          if($validation != null)
            $errors[$rule->propertyName] = $flattenToString ? $validation->message : $validation;
        }
      }

      return $errors;
    }  
}