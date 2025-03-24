<?php 
namespace ValidatePHPCore;

/**
 * Class Validator
 *
 * This class serves as a core utility for validating various data types and input fields. 
 * @package ValidatePHPCore
 * @category Validation
 * @version 1.0.0
 * @author Bishal Shrestha
 * @license MIT License
 * @see packagist link
 * @link https://github.com/shrestha-bishal
 */

class Validator
{
    /**
     * get the validation errors
     */
    public static function getValidationErrors($rules, $data) {
      $errors = [];

      foreach($rules as $rule) 
      {
        $fieldValue = isset($data->{$rule->fieldName}) ? $data->{$rule->fieldName} : null;
        $validation = $rule->validateField($fieldValue);
      
        if($validation != null) {
          $errors[$rule->fieldName] = $validation;
        }
      }

      return $errors;
    }  
}