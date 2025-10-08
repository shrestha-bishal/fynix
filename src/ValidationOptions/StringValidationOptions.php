<?php 
namespace Fynix\ValidationOptions;

class StringValidationOptions extends ValidationOptionsBase {
    public function __construct(
        array $length = ['min' => 2, 'max' => 50],
        bool $isRequired = true,
        bool $includeGenericValidation = true) 
    {
        $this->fieldType = 'string';
        $this->isRequired = $isRequired;   
        $this->includeGenericValidation = $includeGenericValidation;
        $this->length = $length;
    } 
}