<?php 
namespace PhpValidationCore\ValidationOptions;

abstract class ValidationOptions {
    public bool $includeGenericValidation;
    public string $fieldType;
    public bool $isRequired; 
    public array $length = ['min' => null, 'max' => null]; 
}