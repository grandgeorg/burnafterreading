<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// error_reporting(0);
// ini_set('display_errors', '0');
// ini_set('log_errors', '1');

use Bar\Admin;

require __DIR__ . '/../../../BurnAfterReadingApp/vendor/autoload.php';


$admin = new Admin(__DIR__);
$admin->run();