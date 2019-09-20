<?php
/** The OvenMVC Framework **/

define('ENVIRONMENT', isset($_SERVER['OVEN_ENV']) ? $_SERVER['OVEN_ENV'] : 'development');

$lib = 'lib';
$app = 'app';

require_once $lib . '/init.php';
