<?php
class Base_session
{
    public function __construct()
    {
        //session_name('ovenmvc');
        //session_set_cookie_params(strtotime('+30 minutes', 0));
        //session_start();
        //session_regenerate_id(true);
    }

    public function get($key = NULL)
    {
        if ($key) {
            if(!isset($_SESSION[$key])){
              return NULL;   
            }
            return $_SESSION[$key]; 
        }
        return $_SESSION;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function remove($key = NULL)
    {
        if ($key) {
            unset($_SESSION[$key]);
            return false;
        }
        session_destroy();
    }
}
