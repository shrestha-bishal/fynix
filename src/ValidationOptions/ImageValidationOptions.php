<?php 
namespace PhpValidationCore\ValidationOptions;

class ImageValidationOptions extends ValidationOptionsBase {
    public function __construct(
        array $numImage = ['min' => 1, 'max' => 1],
        bool $isRequired = true,
        bool $includeGenericValidation = false,
        public int $maxFileSizeMB = 5)
    {
        $this->fieldType = 'image';
        $this->length = $numImage;
        $this->includeGenericValidation = $includeGenericValidation;
        $this->isRequired = $isRequired;
    }
}