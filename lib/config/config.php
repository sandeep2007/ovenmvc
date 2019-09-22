<?php
$config['base_path'] = str_replace('/', DIRECTORY_SEPARATOR, str_replace('/' . basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_FILENAME']));
$config['app_path'] = str_replace('/', DIRECTORY_SEPARATOR, str_replace('/' . basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_FILENAME']) . '/' . $app);
$config['lib_path'] = str_replace('/', DIRECTORY_SEPARATOR, str_replace('/' . basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_FILENAME']) . '/' . $lib);
$config['base_url'] = '';

define('BASEPATH', $config['base_path']);
define('LIBPATH', $config['lib_path']);
define('APPPATH', $config['app_path']);
define('VERSION', '0.0.8');

$config['time_zone'] = 'Asia/Kolkata';
$config['debug'] = FALSE;
$config['script_path'] = '/';
$config['database'] = FALSE;
$config['session'] = FALSE;

$config['logger'] = FALSE;
$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';

require_once $config['lib_path'] . '\config\includes.php';


