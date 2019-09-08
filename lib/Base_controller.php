<?php

abstract class Base_controller
{
    private static $instance;
    protected $session;
    public function __construct()
    {
        self::$instance =& $this;  
        $this->view = new Base_view();
        $this->request = new Base_request();
        $this->config =& getConfig();
    }

    public static function &init(){ 
        return self::$instance;
    }

    protected function getConfig($key = NULL)
    {
        $config = getConfig();
       // unset($config['db']);
        if ($key) {
            return $config[$key];
        }
        return $config;

    }

    protected function sessionInit(){
        $this->session = new Base_session();
    }

    protected function getModel(){
        require_once LIBPATH . '/Base_model.php';
        $this->model = new Base_model();
    }
}
