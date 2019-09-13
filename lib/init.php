<?php

switch (ENVIRONMENT)
{
    case 'development':
    error_reporting(-1);
    ini_set('display_errors', 1);
    break;
    
    case 'testing':
    case 'production':
    ini_set('display_errors', 0);
    if (version_compare(PHP_VERSION, '5.3', '>='))
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
    }
    else
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
    }
    break;
    
    default:
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'The application environment is not set correctly.';
    exit(1); // EXIT_ERROR
}
require_once $lib . '/config/config.php';
$oven = new Bootstrap();