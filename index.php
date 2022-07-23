<?php

require 'app/config/bootstrap.php';

use App\Cli\Property;

/**
 * This is index File
 * TODO: creation of routes as same happens in framework
 */

// Code to run Property process from CLI
$property = new Property();
$property->process();
