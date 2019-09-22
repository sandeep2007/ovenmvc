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
		// 	return (isset($_GET[$key])) ? $_GET[$key] : NULL;
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

	protected function _clean_array($array, $index = NULL)
	{
		isset($index) or $index = array_keys($array);

		if (is_array($index)) {
			$output = array();
			foreach ($index as $key) {
				$output[$key] = $this->_clean_array($array, $key);
			}

			return $output;
		}

		if (isset($array[$index])) {
			$value = escape_string($array[$index]);
		} elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) 
		{
			$value = $array;
			for ($i = 0; $i < $count; $i++) {
				$key = trim($matches[0][$i], '[]');
				if ($key === '')
				{
					break;
				}

				if (isset($value[$key])) {
					$value = escape_string($value[$key]);
				} else {
					return NULL;
				}
			}
		} else {
			return NULL;
		}

		return $value;
	}
}
