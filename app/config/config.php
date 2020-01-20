<?php
$config['time_zone'] = 'Asia/Kolkata';
$config['version'] = '0.0.1';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
// $config['base_url'] = @$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '';
$config['base_url'] = $protocol.$_SERVER['SERVER_NAME'].'/';
$config['db'] = array(
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'data_store',
);
$config['debug'] = TRUE;
$config['database'] = FALSE;
$config['session'] = TRUE;
$config['script_path'] = 'app';

$config['log_threshold'] = [1,5];
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
