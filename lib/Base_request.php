<?php
class Base_request
{
	public function __construct()
	{ }

	public function method()
	{
		return strtolower($_SERVER['REQUEST_METHOD']);
	}

	public function get($key = NULL)
	{
		// if ($key) {
		//  return (isset($_GET[$key])) ? $_GET[$key] : NULL;
		// }
		// return $_GET;
		return $this->_clean_array($_GET, $key);
	}

	public function post($key = NULL)
	{
		return $this->_clean_array($_POST, $key);
	}

	public function put($key = NULL)
	{
		if ($this->method() === 'put') {
			parse_str(file_get_contents("php://input"), $request);
			return $this->_clean_array($request, $key);
		} else {
			return NULL;
		}
	}

	protected function _clean_array($array_list, $index = NULL)
	{
		if (isset($array_list[$index])) {
			$array_list = $array_list[$index];
		}

		$op = array();
		foreach ($array_list as $key => $array) {

			if (is_array($array)) {
				$op[$key] = _clean_array($array, $key);
			} else {

				$op[$key] = escape_string($array);
			}
		}
		return $op;
	}
}
