<?php 
namespace PhpValidationCore\ValidationOptions;

class ImageValidationOptions extends ValidationOptions {
    public bool $includeGenericValidation = false;
    public string $fieldType = 'image';
    public bool $isRequired = true; 
    /**
     * @var num images
     */
    public array $length = ['min' => 1, 'max' => 1]; 
    public int $maxFileSizeMB = 5;
}