<?php 
namespace PhpValidationCore\ValidationOptions;

class PhoneNumberValidationOptions extends ValidationOptionsBase {
    public function __construct(
        array $length = ['min' => 10, 'max' => 12],
        bool $isRequired = true,
        bool $includeGenericValidation = true)
    {
        $this->fieldType = 'phone';
        $this->isRequired = $isRequired;   
        $this->includeGenericValidation = $includeGenericValidation;
        $this->length = $length;
    }
}