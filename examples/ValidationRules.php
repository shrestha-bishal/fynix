<?php
namespace PhpValidationCore\Examples;

use PhpValidationCore\Validators\StringValidator;

class ValidationRules
{
    public static function validateStaff(Staff $staff): array
    {
        return [
            new StringValidator('Name', $staff->name, 50),
            new StringValidator('Email', $staff->email, 100),
            new StringValidator('Position', $staff->position, 30),
        ];
    }
}