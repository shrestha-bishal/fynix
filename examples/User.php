<?php
use PhpValidationCore\Validator;
use PhpValidationCore\Validators\EmailValidator;
use PhpValidationCore\Validators\ImagesValidator;
use PhpValidationCore\Validators\PhoneNumberValidator;
use PhpValidationCore\Validators\StringValidator;

class User {
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $businessName = null;
    public ?string $streetAddress = null; 
    public ?string $suburb = null;
    public ?string $postcode = null;
    public ?string $state = null;
    public ?string $countryCode = null;
    public ?string $address = null; //full address: typed
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $tyreSize = null;
    public ?string $brand = null;
    public ?string $details = null;
    public ?string $referralSource = null;
    public array $photosUpload = []; //$_FILES

    public function getValidationErrors() : array 
    {
        $rules = 
        [
            new StringValidator('First name', 'firstName', 50),
            new StringValidator('Last name', 'lastName', 50),
            new StringValidator('Business name', 'businessName', 250, 0, false),
            new StringValidator('Address', 'address', 250, 0, false),
            new EmailValidator('Email', 'email', 200, 6),
            new PhoneNumberValidator('Phone number', 'phone'),
            new StringValidator('Tyre size', 'tyreSize', 250),
            new StringValidator('Brand', 'brand', 250, 0, false),
            new StringValidator('Details', 'details', 5000),
            new ImagesValidator('Photos', 'photosUpload', 10, 1, false),
            new StringValidator('Referral Source', 'referralSource', 4, 0, false),
            new StringValidator('Street Address', 'streetAddress', 100, 0, false),
            new StringValidator('Suburb', 'suburb', 100, 0, false),
            new StringValidator('Postcode', 'postcode', 9, 0, false),
            new StringValidator('State', 'state', 15, 0, false),
            new StringValidator('Country Code', 'countryCode', 5, 0, false),
        ];
        
        $validationErrors = Validator::getValidationErrors($rules, $this);
        
        return $validationErrors;
    }
}