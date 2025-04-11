<?php
use ValidatePhpCore\ValidationError;
use ValidatePHPCore\Validator;
use ValidatePHPCore\Validators\EmailValidator;
use ValidatePHPCore\Validators\PasswordValidator;
use ValidatePHPCore\Validators\PhoneNumberValidator;
use ValidatePHPCore\Validators\StringValidator;

class UserRegistration 
{
    public string $accountType = "personal";
    public ?string $businessName = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $email = null;
    public ?string $phoneNumber = null;
    public ?string $password = null;
    public ?string $confirmPassword = null;
    
    public function getValidationErrors() : array 
    {
        $accountTypeValidation = new StringValidator('Account type', 'accountType', 50);
        $businessNameValidation = new StringValidator('Business name', 'businessName', 150);
        $firstNameValidation = new StringValidator('First name', 'firstName', 150);
        $lastNameValidation = new StringValidator('Last name', 'lastName', 150);
        $emailValidation = new EmailValidator('Email', 'email', 200, 6, $isUsername = true);
        $phoneNumberValidation = new PhoneNumberValidator('Phone number', 'phoneNumber');
        $passwordValidation = new PasswordValidator("Password", "password");
        $confirmPasswordValidation = new PasswordValidator("Confirm password", "confirmPassword");

        $rules = [
            $accountTypeValidation,
            $firstNameValidation,
            $emailValidation,
            $phoneNumberValidation,
            $passwordValidation,
            $confirmPasswordValidation
        ];

        if($this->accountType === "business") 
        {
            $this->lastName = null;
            $rules[] = $businessNameValidation;
        }

        if($this->accountType === "personal") 
        {
            $this->businessName = null;
            $rules[] = $lastNameValidation;
        }
        
        $validationErrors = Validator::getValidationErrors($rules, $this);
        
        if($this->password !== $this->confirmPassword) 
            $validationErrors['confirmPassword'] = new ValidationError($confirmPasswordValidation, "Passwords do not match.");

        return $validationErrors;
    }
}