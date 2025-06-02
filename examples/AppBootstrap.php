<?php
use ValidatePhpCore\Examples\Staff;
use ValidatePhpCore\Examples\ValidationRules;
use ValidatePhpCore\ValidationRegistry;

ValidationRegistry::register(Staff::class, [ValidationRules::class, 'validateStaff']);