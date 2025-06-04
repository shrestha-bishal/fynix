<?php 
namespace PhpValidationCore\ValidationOptions;

class PasswordValidationOptions extends ValidationOptionsBase {
    public function __construct(
        array $length = ['min' => 8, 'max' => 30],
        bool $isRequired = true,
        bool $includeGenericValidation = true)
    {
        $this->fieldType = 'password';
        $this->isRequired = $isRequired;   
        $this->includeGenericValidation = $includeGenericValidation;
        $this->length = $length;
    }
}