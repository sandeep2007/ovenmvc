<?php

abstract class Base_controller
{
    protected $session;
    public function __construct()
    {
    
        $this->view = new Base_view();
        $this->request = new Base_request();
        
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
