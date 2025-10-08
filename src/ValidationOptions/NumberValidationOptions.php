<?php 
namespace Fynix\ValidationOptions;

class NumberValidationOptions extends ValidationOptionsBase {
    public function __construct(
        array $length = ['min' => 1, 'max' => 30],
        bool $isRequired = true,
        bool $includeGenericValidation = true,
        public ?array $number = ['min' => null, 'max' => null])
    {
        $this->fieldType = 'number';
        $this->isRequired = $isRequired;   
        $this->includeGenericValidation = $includeGenericValidation;
        $this->length = $length;
    }
}