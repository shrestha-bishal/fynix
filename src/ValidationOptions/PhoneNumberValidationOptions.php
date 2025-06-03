<?php 
namespace PhpValidationCore\ValidationOptions;

class PhoneNumberValidationOptions extends ValidationOptions {
    public bool $includeGenericValidation = true;
    public string $fieldType = 'phone';
    public bool $isRequired = true; 
    public array $length = ['min' => 10, 'max' => 12]; 
}