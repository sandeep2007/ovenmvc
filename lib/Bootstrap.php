<?php
spl_autoload_register(function ($resource) {
    if (file_exists(LIBPATH . '/' . $resource . '.php')) {
        include LIBPATH . '/' . $resource . '.php';
    }

    if (file_exists(LIBPATH . '/libraries/' . $resource . '.php')) {
        include LIBPATH . '/libraries/' . $resource . '.php';
    }
    if (file_exists(APPPATH . '/models/' . $resource . '.php')) {
        include APPPATH . '/models/' . $resource . '.php';
    }
    if (file_exists(APPPATH . '/libraries/' . $resource . '.php')) {
        include APPPATH . '/libraries/' . $resource . '.php';
    }
});

class Bootstrap
{
    public function __construct()
    {

        $uri = uriDecoder();

        //debug($uri);

        if (file_exists($uri['class_path'])) {

            require_once(LIBPATH . '/Base_controller.php');
            require_once($uri['class_path']);

            $instance_ = new $uri['class'];

            if (!method_exists($instance_, $uri['method'])) {
                echo 'Undefined method ' . $uri['method'];
                return false;
            }

            if ($uri['params']) {
                call_user_func_array(array($instance_, $uri['method']), $uri['params']);
            } else {
                call_user_func(array($instance_, $uri['method']));
            }
        } else {
            echo 'Invalid route';
        }
    }
}

function &getConfig()
{
    return $GLOBALS['config'];
}

function &getInstance()
{
    return Base_controller::init();
}


