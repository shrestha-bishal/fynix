<?php 
namespace PhpValidationCore\ValidationOptions;

class EmailValidationOptions extends ValidationOptions {
    public bool $includeGenericValidation = true;
    public string $fieldType = 'email';
    public bool $isRequired = true; 
    public array $length = ['min' => 6, 'max' => 100];
    public bool $isUsername = false;
}