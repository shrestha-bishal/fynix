<?php
namespace Fynix\Validators;

use Fynix\ValidationError;

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

    private int $minImages = 1;
    private int $maxImages = 1;
    private int $maxFileSizeMB = 5;

    public function __construct(
        string $name, 
        string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->withoutGenericValidation();
    }

    public function min(int|float $value): static
    {
        $this->minImages = $this->validateImageCount($value);
        return $this;
    }

    public function max(int|float $value): static
    {
        $this->maxImages = $this->validateImageCount($value);
        return $this;
    }

    public function maxFileSizeMB(int $megabytes): static
    {
        if ($megabytes < 1) {
            throw new \InvalidArgumentException('The maximum file size must be at least 1 MB.');
        }

        $this->maxFileSizeMB = $megabytes;
        return $this;
    }

    private function validateImageCount(int|float $value): int
    {
        if ($value < 0 || $value != (int) $value) {
            throw new \InvalidArgumentException('Image count constraints must be non-negative integers.');
        }

        return (int) $value;
    }

    public function validate(mixed $fieldValue) : ?ValidationError
    {
        $error = null;

        $imageCount = count($fieldValue['name'] ?? []);

        if($imageCount === 0) {
            if ($this->isRequired)
                return new ValidationError($this, "$this->name is required.");

            return null;
        }

        if($imageCount < $this->minImages)
            return new ValidationError($this, "$this->name must contain at least $this->minImages images.");

        if($imageCount > $this->maxImages)
            return new ValidationError($this, "$this->name can contain at most $this->maxImages images.");

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

            $imageValidation = (new ImageValidator($name, $name))->maxFileSizeMB($this->maxFileSizeMB);
            $error = $imageValidation->validate($fieldValueByIndex);
            
            if($error != null) 
            {
                return $error;
            }
        }
        
        return $error;
    }
}