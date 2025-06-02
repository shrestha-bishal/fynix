<?php 
namespace PhpValidationCore\Examples;

class Staff
{
    public string $name;
    public string $email;
    public ?string $position;

    public function __construct(string $name, string $email, ?string $position = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->position = $position;
    }
}