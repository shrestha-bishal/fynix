<?php 
namespace PhpValidationCore;

use Attribute;

/**
 * Attribute to specify a custom validation method for a class.
 *
 * Use this attribute to override the automatic validation rule defined
 * in ValidationRules.php **only if** you need to customize validation behavior.
 *
 * Apply it to classes to indicate which method should be used for validation.
 *
 * Example:
 * #[ValidateWith('customValidationMethod')]
 *
 * @Attribute(Attribute::TARGET_CLASS)
 */

#[Attribute(Attribute::TARGET_CLASS)]
class ValidateWith {
    public function __construct(public string $methodName) {}
}