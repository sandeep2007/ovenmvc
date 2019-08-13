<?php
spl_autoload_register(function ($resource) {
    if (file_exists(APPPATH . '/models/' . $resource . '.php')) {
        include APPPATH . '/models/' . $resource . '.php';
    }
    if (file_exists(APPPATH . '/libraries/' . $resource . '.php')) {
        include APPPATH . '/libraries/' . $resource . '.php';
    }

    if (file_exists(LIBPATH . '/' . $resource . '.php')) {
        include LIBPATH . '/' . $resource . '.php';
    }

    if (file_exists(LIBPATH . '/libraries/' . $resource . '.php')) {
        include LIBPATH . '/libraries/' . $resource . '.php';
    }
});

class Bootstrap
{
    public function __construct()
    {
        
        $url_ = $_SERVER['REQUEST_URI'];
        $sn_ = basename($_SERVER['SCRIPT_NAME']);
        $url_ = $_SERVER['PHP_SELF'];

        if (file_exists(APPPATH . '/routes/web.php')) {
            require_once APPPATH . '/routes/web.php';
        }
        if (file_exists(APPPATH . '/routes/api.php')) {
            require_once APPPATH . '/routes/api.php';
        }
        if (isset($routes)) {
            foreach ($routes as $key => $a) {

                $pattern = str_replace(['(:num)', '(:alpha)', '(:any)'], ['[0-9]+', '[a-zA-Z]+', '[0-9a-zA-Z\-]+'], $key);

                $data = preg_match('#\b' . $pattern . '\b#', $url_, $y);
                if ($data) {

                    $u_ = ltrim(explode($sn_, $url_)[1], '/');
                    $tu_ = NULL;
                    $tr_ = NULL;
                    $u_ = explode('/', $u_);
                    $r_ = explode('/', $key);
                    $a_ = explode('/', $a);

                    if ($r_) {
                        foreach ($r_ as $k_ => $r) {

                            if ($r == '(:num)' || $r == '(:alpha)' || $r == '(:any)') {
                                $tr_[] = $u_[$k_];
                            }
                        }
                    }
                    if ($a_) {
                        $x = 0;
                        foreach ($a_ as $ax) {
                            if (strchr($ax, '$')) {
                                $tu_[] = $tr_[$x];
                                $x++;
                            } else {
                                $tu_[] = $ax;
                            }
                        }
                    }
                    $url_ = $sn_ . '/' . implode('/', $tu_);
                }
            }
        }
        $rs_ = explode($sn_, $url_)[1];
        $rs_ = explode('/', $rs_);
        $d_ = NULL;
        $c_ = NULL;
        $cpath_ = NULL;
        $m_ = NULL;
        $p_ = NULL;
        $x_ = 0;
        foreach ($rs_ as $rs) {
            if ($rs) {
                $d_ .= '/' . $rs;
                
                if (!is_dir(APPPATH . '/controllers' . '/' . $d_)) {
                    if ($x_ === 0) {
                        $cpath_ = APPPATH . '/controllers' . str_replace($rs, ucfirst($rs), $d_) . '.php';
                        $c_ = ucwords($rs);
                    } else if ($x_ === 1) {
                        $m_ = $rs;
                    } else {
                        $p_[] = $rs;
                    }
                    $x_++;
                }
            }
        }

        if (!$c_) {
            $cpath_ = APPPATH . '/controllers' . $d_ . '/Home.php';
            $c_ = 'Home';
            $m_ = 'index';
        }

        if (!$m_) {
            $m_ = 'index';
        }

        if (file_exists($cpath_)) {

            require_once(LIBPATH . '/Base_controller.php');
            require_once($cpath_);

            $instance_ = new $c_;

            if (!method_exists($instance_, $m_)) {
                echo 'Undefined method ' . $m_;
                return false;
            }

            if ($p_) {
                call_user_func_array(array($instance_, $m_), $p_);
            } else {
                call_user_func(array($instance_, $m_));
            }
        } else {
            //echo 'Class ' . $c_ . ' not found';
            echo 'Invalid routes check request uri first';
        }
    }
}
