<?php
error_reporting(0);
ini_set('display_errors', '0');

use Bar\Client;

require __DIR__ . '/../BurnAfterReadingApp/vendor/autoload.php';

$client = new Client(__DIR__);
$client->run();