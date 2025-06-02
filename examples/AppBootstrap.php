<?php
use ValidatePhpCore\Examples\Staff;
use ValidatePhpCore\Examples\ValidationRules;
use ValidatePhpCore\ValidationHandler;

ValidationHandler::register(Staff::class, [ValidationRules::class, 'validateStaff']);