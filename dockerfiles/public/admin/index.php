<?php
error_reporting(0);
ini_set('display_errors', '0');

use Bar\Admin;

require __DIR__ . '/../../BurnAfterReadingApp/vendor/autoload.php';

$admin = new Admin(__DIR__);
$admin->run();