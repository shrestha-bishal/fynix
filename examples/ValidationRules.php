<?php
namespace ValidatePhpCore;

use ValidatePhpCore\Examples\User;
use ValidatePhpCore\Examples\UserRegistration;
use ValidatePhpCore\Validators\StringValidator;

class ValidationRules {
    public static function validateUser(User $user) {
        return [
            new StringValidator('First Name', $user->firstName, 20)
        ];
    }

    public static function validateUserRegistration(UserRegistration $userRegistration) {
        return [
            new StringValidator('First Name', $userRegistration->firstName, 20)
        ];
    }
}