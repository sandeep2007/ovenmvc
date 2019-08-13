<?php 
	class Base_request{
		public function __construct()
		{ }

		public function get($key = NULL)
		{
			if ($key) {
				return (isset($_GET[$key])) ? $_GET[$key] : NULL;
			}
			return $_GET;
		}
	
		public function post($key = NULL)
		{
			if ($key) {
				return (isset($_POST[$key])) ? $_POST[$key] : NULL;
			}
			return $_POST;
		}
		
	}