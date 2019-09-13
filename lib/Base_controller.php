<?php

abstract class Base_controller
{
    private static $instance;
    protected $session;
    protected $data;
    public function __construct()
    {
        self::$instance =& $this;  
        $this->data = NULL;
        $this->view = new Base_view();
        $this->request = new Base_request();
        $this->config =& getConfig();
        if ($this->config['session']) {
            $this->session = new Base_session();
        }
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

    protected function getModel(){
        require_once LIBPATH . '/Base_model.php';
        $this->model = new Base_model();
    }
}
