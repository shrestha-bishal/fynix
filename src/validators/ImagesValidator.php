<?php
namespace ValidatePHPCore\Validators;

use ValidatePhpCore\ValidationError;
use ValidatePHPCore\ValidatorBase;

class ImagesValidator extends ValidatorBase {
    /**
     * Constructor for the ImagesValidator class.
     *
     * @param string  $name       The name of the validation.
     * @param string  $fieldName  The name of the field to be validated.
     * @param int     $minLength  The minimum allowed length for the string.
     * @param int     $maxLength  The maximum allowed length for the string.
     * @param bool    $isRequired Whether the field is required. Defaults to true.
     */

    public function __construct(
        string $name, 
        string $fieldName, 
        int $numMaxImages, 
        int $numMinImages = 1,
        bool $isRequired = true,
        ?int $maxFileSizeMB = null) 
    {
        parent::__construct(
            $name,
            $fieldName,
            $numMinImages,
            $numMaxImages,
            "image",
            $isRequired,
            $includeGenericValidation = false
        );
    }

    public function validate($fieldValue) : ?ValidationError 
    {
        $error = null;

        if($this->isRequired) 
        {
            if(empty($fieldValue['name'][0])) 
                return new ValidationError($this, "$this->name is required.");
        }

        foreach($fieldValue['name'] as $key => $filename) 
        {
            $name = $fieldValue['name'][$key];

            $fieldValueByIndex = 
            [
                'name' => $fieldValue['name'][$key],
                'full_path' => $fieldValue['full_path'][$key],
                'type' => $fieldValue['type'][$key],
                'tmp_name' => $fieldValue['tmp_name'][$key],
                'error' => $fieldValue['error'][$key],
                'size' => $fieldValue['size'][$key]
            ];

            $imageValidation = new ImageValidator($name, $name);
            $error = $imageValidation->validate($fieldValueByIndex);
            
            if($error != null) 
            {
                return $error;
            }
        }
        
        return $error;
    }
}