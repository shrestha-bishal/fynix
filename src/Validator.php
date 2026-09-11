<?php 
namespace Fynix;

use Fynix\Validators\ValidatorBase;

/**
 * Class Validator
 *
 * This class serves as a core utility for validating various data types and input fields. 
 * @package Fynix
 * @category Validation
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
    * @param array<string, ValidatorBase|array> $rules An array of validation rule instances
     * @param object $data              The data object to validate.
     * @param bool   $flattenToString  If true, returns error messages as strings; otherwise, returns ValidationError objects.
     *
     * @return array<string, string[]|ValidationError[]> An associative array of validation errors keyed by property name.
     */
    /**
     * @param array<string|int, ValidatorBase|array<mixed>> $rules
    * @param array<string, bool>|null $visited
     * @return array<string|int, mixed>
     */
    public static function getValidationErrors(
        array $rules,
        object $data,
        bool $flattenToString = true,
        ?array &$visited = null
    ) : array {
      $visited ??= [];
      $hash = spl_object_hash($data);

      if (isset($visited[$hash]))
        return [];

      $visited[$hash] = true;
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
                if (!array_key_exists($index, $propertyValue)) {
                  if (!isset($errors[$key]) || !is_array($errors[$key])) {
                    $errors[$key] = [];
                  }

                  $errors[$key][$index] = self::formatError(
                      "$key.$index",
                      'Missing object item.',
                      'object.missing',
                      $flattenToString
                  );
                  continue;
                }

                $nestedItem = $propertyValue[$index];

                if (!is_object($nestedItem)) {
                  if (!isset($errors[$key]) || !is_array($errors[$key])) {
                    $errors[$key] = [];
                  }

                  $errors[$key][$index] = self::formatError(
                    "$key.$index",
                    'Invalid item -expected object.',
                    'object.invalid',
                    $flattenToString
                  );
                    continue;
                }

                $nestedErrors = self::getValidationErrors($nestedRules, $nestedItem, $flattenToString, $visited);
                if (!empty($nestedErrors)) {
                    if (!isset($errors[$key]) || !is_array($errors[$key])) {
                      $errors[$key] = [];
                    }

                    $errors[$key][$index] = $nestedErrors;
                }
              }
          }
          
          // case: single nested object
          else if (is_object($propertyValue)) 
          {
            $nestedErrors = self::getValidationErrors($rule, $propertyValue, $flattenToString, $visited);
            
            if(!empty($nestedErrors))
              $errors[$key] = $nestedErrors;
          }

          // else: not valid structure
          else {
              $errors[$key] = self::formatError(
                (string) $key,
                'Invalid value -expected object or array of objects.',
                'object.invalid',
                $flattenToString
              );
          }

          continue;
        }

        /** @var ValidatorBase $rule */
        $rule = $rule;
        $field = $rule->propertyName();
        $fieldValue = isset($data->{$field}) ? $data->{$field} : null;
        $validations = $rule->validateFieldAll($fieldValue);

        if(!empty($validations)) {
          if ($flattenToString) {
              $messages = array_map(static fn(ValidationError $error): string => $error->message, $validations);
              $errors[$field] = count($messages) === 1 ? $messages[0] : $messages;
          } else {
              $errors[$field] = count($validations) === 1 ? $validations[0] : $validations;
          }
        }
      }

      return $errors;
      }

      private static function formatError(
        string $field,
        string $message,
        string $code,
        bool $flattenToString
      ): string|ValidationError {
        return $flattenToString ? $message : ValidationError::forField($field, $message, $code);
      }
}