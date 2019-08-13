<?php 
	class Base_view{
		public function __construct()
		{ }

		public function copy($page, $data = NULL, $return = FALSE)
		{
	
			if (file_exists('app/views/' . $page . '.php')) {
				if ($data) {
					extract($data);
				}
	
				if ($return === TRUE) {
					ob_clean();
					include('app/views/' . $page . '.php');
					return ob_get_clean();
				} else {
					include('app/views/' . $page . '.php');
				}
			} else {
				die("Error while loading page - $page");
			}
		}
		
	}