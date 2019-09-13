<?php
if ($config['session']) {
    session_name('ovenmvc');
    session_set_cookie_params(strtotime('+30 minutes', 0));
    session_start();
    //if (!isXHR()) {
    //session_regenerate_id(true);  
    //}
}
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
            $config = &getConfig();
            require_once(LIBPATH . '/Base_controller.php');
            require_once($uri['class_path']);

            if ($config['database']) {
                new DB;
            }

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

function &getModel()
{
    $config = &getConfig();
    if ($config['database']) {
        $m = new DB;
        return $m;
    } else {
        return NULL;
    }
}

if (!function_exists('is_really_writable')) {
    /**
     * Tests for file writability
     *
     * is_writable() returns TRUE on Windows servers when you really can't write to
     * the file, based on the read-only attribute. is_writable() is also unreliable
     * on Unix servers if safe_mode is on.
     *
     * @link	https://bugs.php.net/bug.php?id=54709
     * @param	string
     * @return	bool
     */
    function is_really_writable($file)
    {
        // If we're on a Unix server with safe_mode off we call is_writable
        if (DIRECTORY_SEPARATOR === '/' && (is_php('5.4') or !ini_get('safe_mode'))) {
            return is_writable($file);
        }

        /* For Windows servers and safe_mode "on" installations we'll actually
		 * write a file then read it. Bah...
		 */
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE) {
                return FALSE;
            }

            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        } elseif (!is_file($file) or ($fp = @fopen($file, 'ab')) === FALSE) {
            return FALSE;
        }

        fclose($fp);
        return TRUE;
    }
}

if (!function_exists('log_message')) {
    function log_message($level, $message)
    {
        static $_log;

        if ($_log === NULL) {
            $_log = new Base_log;
        }

        $_log->write_log($level, $message);
    }
}

if (!function_exists('set_status_header')) {
    /**
     * Set HTTP Status Header
     *
     * @param	int	the status code
     * @param	string
     * @return	void
     */
    function set_status_header($code = 200, $text = '')
    {
        // if (is_cli())
        // {
        // 	return;
        // }

        if (empty($code) or !is_numeric($code)) {
            show_error('Status codes must be numeric', 500);
        }

        if (empty($text)) {
            is_int($code) or $code = (int) $code;
            $stati = array(
                100    => 'Continue',
                101    => 'Switching Protocols',

                200    => 'OK',
                201    => 'Created',
                202    => 'Accepted',
                203    => 'Non-Authoritative Information',
                204    => 'No Content',
                205    => 'Reset Content',
                206    => 'Partial Content',

                300    => 'Multiple Choices',
                301    => 'Moved Permanently',
                302    => 'Found',
                303    => 'See Other',
                304    => 'Not Modified',
                305    => 'Use Proxy',
                307    => 'Temporary Redirect',

                400    => 'Bad Request',
                401    => 'Unauthorized',
                402    => 'Payment Required',
                403    => 'Forbidden',
                404    => 'Not Found',
                405    => 'Method Not Allowed',
                406    => 'Not Acceptable',
                407    => 'Proxy Authentication Required',
                408    => 'Request Timeout',
                409    => 'Conflict',
                410    => 'Gone',
                411    => 'Length Required',
                412    => 'Precondition Failed',
                413    => 'Request Entity Too Large',
                414    => 'Request-URI Too Long',
                415    => 'Unsupported Media Type',
                416    => 'Requested Range Not Satisfiable',
                417    => 'Expectation Failed',
                422    => 'Unprocessable Entity',
                426    => 'Upgrade Required',
                428    => 'Precondition Required',
                429    => 'Too Many Requests',
                431    => 'Request Header Fields Too Large',

                500    => 'Internal Server Error',
                501    => 'Not Implemented',
                502    => 'Bad Gateway',
                503    => 'Service Unavailable',
                504    => 'Gateway Timeout',
                505    => 'HTTP Version Not Supported',
                511    => 'Network Authentication Required',
            );

            if (isset($stati[$code])) {
                $text = $stati[$code];
            } else {
                show_error('No status text available. Please check your status code number or supply your own message text.', 500);
            }
        }

        if (strpos(PHP_SAPI, 'cgi') === 0) {
            header('Status: ' . $code . ' ' . $text, TRUE);
            return;
        }

        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], array('HTTP/1.0', 'HTTP/1.1', 'HTTP/2'), TRUE))
            ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        header($server_protocol . ' ' . $code . ' ' . $text, TRUE, $code);
    }
}



if (!function_exists('_error_handler')) {
    /**
     * Error Handler
     *
     * This is the custom error handler that is declared at the (relative)
     * top of CodeIgniter.php. The main reason we use this is to permit
     * PHP errors to be logged in our own log files since the user may
     * not have access to server logs. Since this function effectively
     * intercepts PHP errors, however, we also need to display errors
     * based on the current error_reporting level.
     * We do that with the use of a PHP error template.
     *
     * @param	int	$severity
     * @param	string	$message
     * @param	string	$filepath
     * @param	int	$line
     * @return	void
     */
    function _error_handler($severity, $message, $filepath, $line)
    {
        $is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

        // When an error occurred, set the status header to '500 Internal Server Error'
        // to indicate to the client something went wrong.
        // This can't be done within the $_error->show_php_error method because
        // it is only called when the display_errors flag is set (which isn't usually
        // the case in a production environment) or when errors are ignored because
        // they are above the error_reporting threshold.
        if ($is_error) {
            set_status_header(500);
        }

        // Should we ignore the error? We'll get the current error_reporting
        // level and add its bits with the severity bits to find out.
        if (($severity & error_reporting()) !== $severity) {
            return;
        }

        $_error = new Base_exceptions;
        $_error->log_exception($severity, $message, $filepath, $line);

        // Should we display the error?
        if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
            $_error->show_php_error($severity, $message, $filepath, $line);
        }

        // If the error is fatal, the execution of the script should be stopped because
        // errors can't be recovered from. Halting the script conforms with PHP's
        // default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
        if ($is_error) {
            exit(1); // EXIT_ERROR
        }
    }
}

// ------------------------------------------------------------------------

if (!function_exists('_exception_handler')) {
    /**
     * Exception Handler
     *
     * Sends uncaught exceptions to the logger and displays them
     * only if display_errors is On so that they don't show up in
     * production environments.
     *
     * @param	Exception	$exception
     * @return	void
     */
    function _exception_handler($exception)
    {
        $_error = new Base_exceptions;
        $_error->log_exception('error', 'Exception: ' . $exception->getMessage(), $exception->getFile(), $exception->getLine());

        //is_cli() OR set_status_header(500);
        set_status_header(500);
        // Should we display the error?
        if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
            $_error->show_exception($exception);
        }

        exit(1); // EXIT_ERROR
    }
}

// ------------------------------------------------------------------------

if (!function_exists('_shutdown_handler')) {
    /**
     * Shutdown Handler
     *
     * This is the shutdown handler that is declared at the top
     * of CodeIgniter.php. The main reason we use this is to simulate
     * a complete custom exception handler.
     *
     * E_STRICT is purposively neglected because such events may have
     * been caught. Duplication or none? None is preferred for now.
     *
     * @link	http://insomanic.me.uk/post/229851073/php-trick-catching-fatal-errors-e-error-with-a
     * @return	void
     */
    function _shutdown_handler()
    {
        $last_error = error_get_last();
        if (
            isset($last_error) && ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING))
        ) {
            _error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}

set_error_handler('_error_handler');
set_exception_handler('_exception_handler');
register_shutdown_function('_shutdown_handler');
