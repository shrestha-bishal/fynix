<?php
declare(strict_types=1);

namespace Fynix\Validators;

use DateTimeInterface;
use Fynix\ValidationError;

class DateTimeValidator extends ValidatorBase
{
    protected ?string $format = null;

    protected function __construct(string $name, string $propertyName)
    {
        parent::__construct($name, $propertyName);
    }

    public function format(string $format): static
    {
        return $this->with('format', $format);
    }

    public function validate(mixed $fieldValue = null): ?ValidationError
    {
        if ($this->boundObject !== null) return $this->validateBound($fieldValue);
        if ($fieldValue instanceof DateTimeInterface) {
            return null;
        }

        if (!is_string($fieldValue)) {
            return new ValidationError($this, "$this->name must be a valid date or date-time.", 'datetime.invalid');
        }

        $date = $this->format === null
            ? date_create($fieldValue)
            : DateTimeValidator::createFromFormat($this->format, $fieldValue);

        if ($date === false || ($this->format !== null && $date->format($this->format) !== $fieldValue)) {
            return new ValidationError($this, "$this->name must be a valid date or date-time.", 'datetime.invalid');
        }

        return null;
    }

    private static function createFromFormat(string $format, string $value): DateTimeInterface|false
    {
        $date = date_create_from_format($format, $value);
        $errors = date_get_last_errors();

        if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
            return false;
        }

        return $date;
    }
}