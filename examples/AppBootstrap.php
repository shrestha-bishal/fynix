<?php

use PhpValidationCore\Examples\Staff;
use PhpValidationCore\Examples\ValidationRules;
use PhpValidationCore\ValidationRegistry;

ValidationRegistry::register(Staff::class, [ValidationRules::class, 'validateStaff']);