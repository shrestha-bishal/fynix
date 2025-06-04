<?php 
namespace PhpValidationCore\ValidationOptions;

class EmailValidationOptions extends ValidationOptionsBase {
    public function __construct(
        array $length = ['min' => 6, 'max' => 100],
        bool $isRequired = true,
        bool $includeGenericValidation = true,
        public bool $isUsername = false) 
    { 
        $this->fieldType = 'email';
        $this->isRequired = $isRequired;   
        $this->includeGenericValidation = $includeGenericValidation;
        $this->length = $length;
    }
}