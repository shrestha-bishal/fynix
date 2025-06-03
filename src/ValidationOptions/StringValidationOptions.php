<?php 
namespace PhpValidationCore\ValidationOptions;

class StringValidationOptions extends ValidationOptions {
    public bool $includeGenericValidation = true;
    public string $fieldType = 'string';
    public bool $isRequired = true; 
    public array $length = ['min' => 2, 'max' => 50]; 
}