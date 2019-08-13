<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once APPPATH . '/config/config.php';
require_once LIBPATH . '/helpers/helper_core.php';
require_once LIBPATH . '/Bootstrap.php';

if(file_exists(APPPATH . '/helpers/helper_functions.php')){
    require_once APPPATH . '/helpers/helper_functions.php';
}

if(file_exists(APPPATH . '/base/Model.php')){
    require_once APPPATH . '/base/Model.php';
}

if(file_exists(APPPATH . '/base/Controller.php')){
    require_once APPPATH . '/base/Controller.php';
}





