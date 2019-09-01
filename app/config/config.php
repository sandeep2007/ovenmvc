<?php

$config['version'] = '0.0.1';
$config['base_url'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/oven';
$config['db'] = array(
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'posmenu',
);
$config['debug'] = TRUE;
$config['rest_debug'] = TRUE;

$config['script_path'] = 'app';
