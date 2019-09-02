<?php
$config['base_path'] = str_replace('/' . basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_FILENAME']);
$config['app_path'] = str_replace('/' . basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_FILENAME']) . '/' . $app;
$config['lib_path'] = str_replace('/' . basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_FILENAME']) . '/' . $lib;
$config['base_url'] = '';

define('BASEPATH', $config['base_path']);
define('LIBPATH', $config['lib_path']);
define('APPPATH', $config['app_path']);
define('ENVIRONMENT', 'development');
define('VERSION', '0.0.8');

$config['debug'] = FALSE;
$config['script_path'] = '/';

require_once $config['lib_path'] . '/config/includes.php';


