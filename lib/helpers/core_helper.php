<?php

if (!function_exists('uriDecoder')) {
    function uriDecoder()
    {
        $result = NULL;
        $sn_ = basename($_SERVER['SCRIPT_NAME']);
        $url_ = $_SERVER['PHP_SELF'];

        if (file_exists(APPPATH . '/routes/web.php')) {
            require APPPATH . '/routes/web.php';
        }
        if (file_exists(APPPATH . '/routes/api.php')) {
            require APPPATH . '/routes/api.php';
        }

        // if(array_key_exists('/', $routes)){
        //     die('Invalid routes found');
        // }

        //debug($GLOBALS);
       // echo $url_.'</br>';
        if (isset($routes)) { 
            foreach ($routes as $key => $a) {

                $pattern = str_replace(['(:num)', '(:alpha)', '(:any)'], ['[0-9]+', '[a-zA-Z]+', '[0-9a-zA-Z\-]+'], $key);
                $pattern = ltrim($pattern, '/');
                $pattern = (!empty($pattern)) ? $pattern : NULL;

                $em_c = ltrim(explode($sn_, $url_)[1], '/');
                $data = preg_match('#^' . $pattern . '$#', $em_c, $y);

                $pattern = str_replace('(:default)', '/', $pattern);
                $pattern = ltrim($pattern, '/');
                //taks pending for default route

                if (!empty($y[0]) || (empty($em_c) && !$pattern)) {

                    //echo 'Data = '.($data);echo '<br/>';
                    //echo 'Y = '.($y[0]);echo '<br/>';
                    //echo 'EMC = '.($em_c);echo '<br/>';
                    //echo 'url_ = '.($url_);echo '<br/>';
                    //echo 'Pattern = '.($pattern);echo '<br/>';
                    //$u_ = ltrim(explode($sn_, $url_)[1], '/');
                    $u_ = $em_c;
                    $tu_ = [];
                    $tr_ = NULL;
                    $u_ = explode('/', $u_);
                    $r_ = explode('/', ltrim($key, '/'));
                    $a_ = explode('/', $a);

                    if ($r_) {
                        foreach ($r_ as $k_ => $r) {

                            if ($r == '(:num)' || $r == '(:alpha)' || $r == '(:any)') {
                                $tr_[] = $u_[$k_];
                            }
                        }
                    }
                    //echo '<pre>';
                    //print_r($tr_);
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
                    // print_r($tu_);
                    $url_ = $sn_ . '/' . implode('/', $tu_);
                }
            }
        }
       // echo $url_."<br/>";
        $rs_ = explode($sn_, $url_)[1];
        $rs_ = explode('/', $rs_);
        $d_ = NULL;
        $c_ = NULL;
        $cpath_ = NULL;
        $m_ = NULL;
        $p_ = NULL;
        $x_ = 0;
        $df_ = NULL;

        foreach ($rs_ as $rs) {
            if ($rs) {
                $d_ .= '/' . $rs;

                if (!is_dir(APPPATH . '/controllers' . '/' . $d_)) {
                    if ($x_ === 0) {

                        $cpath_ = APPPATH . '/controllers' . str_replace($rs, ucfirst($rs), $d_) . '.php';

                        $df_ = str_replace($rs, '', $d_);
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
            $cpath_ = APPPATH . '/controllers' . ((rtrim($df_, '/')) ? rtrim($df_, '/') : '') . '/Home.php';
            $c_ = 'Home';
            $m_ = 'index';
        }

        if (!$m_) {
            $m_ = 'index';
        }

        $result = array(
            'class_path' => $cpath_,
            'directory' => (rtrim($df_, '/')) ? rtrim($df_, '/') : '/',
            'class' => $c_,
            'method' => $m_,
            'params' => $p_,
        );

        return $result;
    }
}



if (!function_exists('baseUrl')) {
    function baseUrl()
    {
        return $GLOBALS['config']['base_url'];
    }
}



if (!function_exists('debug')) {

    function debug($message = NULL)
    {
        if (!empty($message)) {
            echo "<pre>";
            print_r($message);
            echo "</pre>";
            die;
        } else {
            echo NULL;
        }
    }
}

if (!function_exists('directory_info')) {
    function directory_info($source_dir, $directory_depth = 0, $hidden = FALSE)
    {
        if ($fp = @opendir($source_dir)) {
            $filedata = array();
            $new_depth = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            $x = 0;
            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' or $file === '..' or ($hidden === FALSE && $file[0] === '.')) {
                    continue;
                }

                is_dir($source_dir . $file) && $file .= DIRECTORY_SEPARATOR;

                if (($directory_depth < 1 or $new_depth > 0) && is_dir($source_dir . $file)) {
                    $filedata[$file]['data'] = directory_info($source_dir . $file, $new_depth, $hidden);
                    $filedata[$file]['permission'] = substr(decoct(fileperms($source_dir)), -4);
                    $filedata[$file]['type'] = "dir";
                } else {
                    $filedata[$x]['data'] = $file;
                    $filedata[$x]['permission'] = substr(decoct(fileperms($source_dir . $file)), -4);
                    $filedata[$x]['type'] = pathinfo($file, PATHINFO_EXTENSION);
                    //chmod($source_dir . $file,0755);
                }
                $x++;
            }

            closedir($fp);
            return $filedata;
        }

        return FALSE;
    }
}

if (!function_exists('console')) {

    function console($string = null)
    {

        if (ENVIRONMENT === 'development') {
            $d = json_encode($string);
            echo $cn = "<script>console.log(" . $d . ");</script>";
        }
    }
}

if (!function_exists('alert')) {

    function alert($string = null)
    {
        if ($string != null) {
            $d = json_encode($string);
            echo "<script>alert(" . $d . ");</script>";
        }
    }
}


if (!function_exists('selected')) {

    function selected($p1, $p2)
    {
        if ($p1 == $p2) {
            echo 'selected';
        }
    }
}

if (!function_exists('checked')) {

    function checked($p1, $p2)
    {
        if ($p1 == $p2) {
            echo 'checked';
        }
    }
}

if (!function_exists('loadScript')) {

    function loadScript(array $vars = NULL, array $library = NULL, $controller = NULL, $method = NULL)
    {
        $data = NULL;
        $APP = &getInstance();
        $uri = uriDecoder();
        if ($controller == NULL) {
            $controller = $uri['class'];
        }
        if ($method == NULL) {
            $method = $uri['method'];
        }

        if (ENVIRONMENT === 'development' && $APP->config['debug'] === TRUE && !empty($controller) && !empty($method)) {
            echo "<script>console.warn('%c [Class: %c" . $controller . "%c, Method: %c" . $method . "%c]', 'color:orange', 'color:red;font-weight:bold', 'color:orange', 'color:red;font-weight:bold', 'color:orange');</script>";
        }

        echo '<script>js_obj = {};</script>';

        $root_path = (BASEPATH) ? BASEPATH . '/' : '';
        
        $path = ($uri['directory'] !== '/') ? $uri['directory'] . '/' : '';

       $script_path = ((!empty($APP->config['script_path'])) ? $APP->config['script_path'] . "/" : "");
      
        if ($vars) {
            foreach ($vars as $key => $value) {
                $data['js_obj']['var'][$key] = $value;
            }
        }

        $data['js_obj']['var']['base_url'] = baseUrl();
        $data['js_obj']['var']['version'] = $APP->config['version'];

        if (file_exists($root_path . $script_path . 'scripts/lib.js')) {
            echo '<script type="text/javascript" src="' . baseUrl() .'/'. $script_path . 'scripts/lib.js"></script>';
        }

        if ($library) {
            foreach ($library as $ct) {
                if ($ct != 'lib' && file_exists($root_path . $script_path . 'scripts/' . $path . $ct . '.js')) {
                    $data['js_obj']['url'][$ct] = baseUrl() .'/'. $script_path . 'scripts/' . $path . $ct . '.js';
                } else {
                    //$data['js_obj']['url'][$ct] = baseUrl() . $script_path . 'scripts/' . $ct . '.js';
                }
            }
        }

        if ($controller != NULL && $method != NULL) {

            if (file_exists($root_path . $script_path . 'scripts/' . $path . 'main.js')) {
                $data['js_obj']['url']['main'] = baseUrl() .'/'. $script_path . 'scripts/' . $path . 'main.js';
            }
            if (file_exists($root_path . $script_path . 'scripts/' . $path . $controller . '.js')) {
                $data['js_obj']['url'][$controller] = baseUrl() .'/'. $script_path . 'scripts/' . $path . $controller . '.js';
            }
            if (file_exists($root_path . $script_path . 'scripts/' . $path . $controller . '/' . $method . '.js')) {
                $data['js_obj']['url'][$controller . '/' . $method] = baseUrl() .'/'. $script_path . 'scripts/' . $path . $controller . '/' . $method . '.js';
            }
        } else if ($controller != NULL) {
            if (file_exists($root_path . $script_path . 'scripts/' . $path . 'main.js')) {
                $data['js_obj']['url']['main'] = baseUrl() .'/'. $script_path . 'scripts/' . $path . 'main.js';
            }
            if (file_exists($root_path . $script_path . 'scripts/' . $path . $controller . '.js')) {
                $data['js_obj']['url'][$controller] = baseUrl().'/' . $script_path . 'scripts/' . $path . $controller . '.js';
            }
        } else if ($controller == NULL) {
            if (file_exists($root_path . $script_path . 'scripts/' . $path . 'main.js')) {
                $data['js_obj']['url']['main'] = baseUrl() .'/'. $script_path . 'scripts/' . $path . 'main.js';
            }
        }

        echo '<script>js_obj = Object.assign(js_obj, ' . json_encode($data['js_obj']['var']) . ');</script>';

        if ((@$data['js_obj']['url']) ? count($data['js_obj']['url']) : 0) {
            foreach ($data['js_obj']['url'] as $key => $value) {
                echo '<script type="text/javascript" src="' . $value . '"></script>';
            }
        }
    }
}

if (!function_exists('url_string')) {

    function url_string($string)
    {
        return strtolower(preg_replace('/-+/', '-', preg_replace('/[^A-Za-z0-9\-\']/', '-', $string)));
    }
}

if (!function_exists('format_number')) {

    function format_number($value, $format = TRUE)
    {
        if ($format == false) {
            return round((float) $value, 2);
        }
        return number_format(round((float) $value, 2), 2);
    }
}

if (!function_exists('get_percent')) {

    function get_percent($total_value, $obtained_value, $format = true)
    {

        if (!empty($total_value) && !empty($obtained_value)) {
            $final_value = (((float) $obtained_value / (float) $total_value) * 100);
            if ($format == false) {
                return round((float) $final_value, 2);
            }
            return number_format(round((float) $final_value, 2), 2);
        } else {
            return number_format(0, 2);
        }
    }
}

if (!function_exists('get_value')) {

    function get_value($value, $percent, $format = true)
    {

        if (!empty($value) && !empty($percent)) {
            $final_value = (((float) $value * (float) $percent) / 100);

            if ($format == false) {
                return round((float) $final_value, 2);
            }
            return number_format(round((float) $final_value, 2), 2);
        } else {
            return number_format(0, 2);
        }
    }
}

if (!function_exists('search_revisions')) {
    function search_revisions($dataArray = NULL, $search_value, $key_to_search)
    {
        // This function will search the revisions for a certain value
        // related to the associative key you are looking for.
        $keys = array();
        if ($dataArray) {
            foreach ($dataArray as $key => $cur_value) {
                if ($cur_value[$key_to_search] == $search_value) {
                    $keys[] = $key;
                }
            }
        }

        return $keys;
    }
}

if (!function_exists('parse_camel_case')) {
    function parse_camel_case($str)
    {
        return preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]|[0-9]{1,}/', ' $0', $str);
    }
}
if (!function_exists('camel_case_to_snake')) {
    function camel_case_to_snake($str)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}

if (!function_exists('id')) {
    function id($type = NULL)
    {
        $id = NULL;
        if ($type == NULL) {
            $id = NULL;
        }
        return $id;
    }
}

if (!function_exists('current_date')) {
    function current_date()
    {
        $APP = &getInstance();
        return (isset($APP->config['date'])) ? $APP->config['date'] : date("Y-m-d H:i:s");
    }
}

if (!function_exists('random_number')) {
    function random_number()
    {
        return substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    }
}

if (!function_exists('encoded_random_number')) {
    function encoded_random_number()
    {
        return base64_encode(substr(hash('sha256', mt_rand() . microtime()), 0, 20));
    }
}
