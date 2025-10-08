<?php
namespace Fynix\Tests;

use PHPUnit\Framework\TestCase;
use Fynix\Examples\User;

final class UserTest extends TestCase {
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testValidUserRegistration(): void
    {
        $this->user->firstName = "John";
        $this->user->lastName = "Doe";
        $this->user->email = "john.doe@example.com";
        $this->user->phone = "+1234567890";
        $this->user->streetAddress = "123 Main St";
        $this->user->suburb = "Suburb";
        $this->user->postcode = "12345";
        $this->user->state = "StateName";
        $this->user->countryCode = "AU";
        $this->user->address = "123 Main St, Suburb";
        $this->user->referralSource = "Web";
        $this->user->photosUpload = []; // Assume empty but valid since it's not required

        $errors = $this->user->getValidationErrors();
        $this->assertEmpty($errors, "Valid user data should produce no validation errors.");
    }
}