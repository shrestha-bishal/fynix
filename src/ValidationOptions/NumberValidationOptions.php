<?php 
namespace PhpValidationCore\ValidationOptions;

class NumberValidationOptions extends ValidationOptions {
    public bool $includeGenericValidation = true;
    public string $fieldType = 'number';
    public bool $isRequired = true; 
    public array $length = ['min' => 1, 'max' => 30]; 
    public ?array $number = ['min' => null, 'max' => null];
}