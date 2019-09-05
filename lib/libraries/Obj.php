<?php
	class Obj
	{
		private $request;
		public function __construct()
		{
			$this->request = new stdClass();
		}
		public function get()
		{
			$request = $this->request;
			return (array) $request;
		}
		public function set($key, $value, $notNull = FALSE)
		{
			if ($notNull && $value == null) {
				$this->request->$key = '';
				} else {
				$this->request->$key = $value;
			}
		}
	}		