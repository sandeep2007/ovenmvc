<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once APPPATH . '/config/config.php';
require_once LIBPATH . '/helpers/core_helper.php';
require_once LIBPATH . '/Bootstrap.php';

if(file_exists(APPPATH . '/helpers/functions_helper.php')){
    require_once APPPATH . '/helpers/functions_helper.php';
}

if(file_exists(APPPATH . '/base/Model.php')){
    require_once APPPATH . '/base/Model.php';
}

if(file_exists(APPPATH . '/base/Controller.php')){
    require_once APPPATH . '/base/Controller.php';
}





