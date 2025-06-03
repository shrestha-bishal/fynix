<?php
namespace PhpValidationCore\Validators;

use PhpValidationCore\ValidationError;
use PhpValidationCore\ValidationOptions\ImageValidationOptions;
use PhpValidationCore\ValidatorBase;

class ImagesValidator extends ValidatorBase {
    /**
     * Constructor for the ImagesValidator class.
     *
     * @param string  $name       The name of the validation.
     * @param string  $propertyName  The name of the field to be validated.
     * @param int     $minLength  The minimum allowed length for the string.
     * @param int     $maxLength  The maximum allowed length for the string.
     * @param bool    $isRequired Whether the field is required. Defaults to true.
     */

    public ImageValidationOptions $options;

    public function __construct(
        string $name, 
        string $propertyName, 
        ?ImageValidationOptions $options = null) 
    {
        $options ??= new ImageValidationOptions();
        $this->options = $options;
        parent::__construct($name, $propertyName, $options);
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

            $imageValidation = new ImageValidator($name, $name, $this->options);
            $error = $imageValidation->validate($fieldValueByIndex);
            
            if($error != null) 
            {
                return $error;
            }
        }
        
        return $error;
    }
}