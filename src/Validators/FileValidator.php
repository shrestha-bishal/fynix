<?php
declare(strict_types=1);

namespace Fynix\Validators;

use Fynix\ValidationError;

class FileValidator extends ValidatorBase
{
    protected int $maxFileSizeMB = 10;
    /** @var list<string>|null */
    protected ?array $allowedExtensions = null;

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
        $this->includeGenericValidation = false;
    }

    public function maxFileSizeMB(int $megabytes): static
    {
        if ($megabytes < 1) {
            throw new \InvalidArgumentException('The maximum file size must be at least 1 MB.');
        }

        return $this->with('maxFileSizeMB', $megabytes);
    }

    /** @param list<string> $extensions */
    public function extensions(array $extensions): static
    {
        return $this->with('allowedExtensions', array_map('strtolower', $extensions));
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        if (($fieldValue === null || $fieldValue === '') && !$this->isRequired) {
            return null;
        }

        if (!is_array($fieldValue)) {
            return new ValidationError($this, "$this->name must be an uploaded file.", 'file.invalid');
        }

        $filename = $fieldValue['name'] ?? '';
        if ($filename === '') {
            return $this->isRequired ? new ValidationError($this, "$this->name is required.", 'required') : null;
        }

        if (($fieldValue['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || !isset($fieldValue['size'], $fieldValue['tmp_name'])) {
            return new ValidationError($this, "$filename is not a valid upload.", 'file.invalid');
        }

        if (!is_string($fieldValue['tmp_name']) || !is_file($fieldValue['tmp_name'])) {
            return new ValidationError($this, "$filename is not readable.", 'file.invalid');
        }

        if ($fieldValue['size'] > $this->maxFileSizeMB * 1024 * 1024) {
            return new ValidationError($this, "$filename exceeds the maximum size limit of $this->maxFileSizeMB MB.", 'file.size');
        }

        if ($this->allowedExtensions !== null
            && !in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), $this->allowedExtensions, true)) {
            return new ValidationError($this, "$filename has an invalid extension.", 'file.extension');
        }

        return null;
    }
}