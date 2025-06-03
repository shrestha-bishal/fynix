<?php 
namespace PhpValidationCore\ValidationOptions;

class PasswordValidationOptions extends ValidationOptions {
    public bool $includeGenericValidation = true;
    public string $fieldType = 'password';
    public bool $isRequired = true; 
    public array $length = ['min' => 8, 'max' => 30]; 
}