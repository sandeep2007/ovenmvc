<?php
class Base_view
{
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

	public function uri_segment($segment)
	{
		$segment = $segment - 1;
		$sn_ = basename($_SERVER['SCRIPT_NAME']);
		$uri = trim(explode($sn_, $_SERVER['PHP_SELF'])[1], '/');
		$uri = explode('/', $uri);
		return (isset($uri[$segment])) ? $uri[$segment] : NULL;
	}
	public function pagination($limit, $total, $page_number = NULL, array $config = array())
	{
		$page_number = (empty(trim($page_number)) || $page_number == NULL) ? 1 : $page_number;
		$total_pages = ceil($total / $limit);
		$config['link_limit'] = (isset($config['link_limit'])) ? $config['link_limit'] : 2;
		$config['link_limit'] = ($config['link_limit'] * 2) + 1;
		$pagLink = "";
		$page_number = (int) $page_number;
		$link_arr = array();
		if (isset($config['link_limit'])) {
			//$link_arr[1] = 1;
			$link_arr['first'] = 'first';
			$link_arr['prev'] = '<';
			$i = 1;
			if ($page_number != 1) {
				$i = $page_number;
				$last_link = $config['link_limit'] + $page_number + 1;
			} else {
				$last_link = $config['link_limit'] + 1;
			}

			if($total_pages == $page_number){
				$link_arr[$total_pages] = (string) $total_pages; //
			}
			else{
				for ($i; $i < $last_link; $i++) {
					if ($total_pages >= $i) {
						$link_arr[$i] = (string) $i;
					}
				}
			}
			
			/* 
			if($page_number == 1){
				for($i=$page_number+1;$i<$page_number+$config['link_limit']+1;$i++){
					$link_arr[$i] = (string)$i;
				}
			}
			else if($page_number <= $total_pages-$config['link_limit']){
			for($i=$page_number-1;$i<$page_number+$config['link_limit']-1;$i++){
			if($i == 1){
			$link_arr[1] = 'first';
			}
			else{
			$link_arr[$i] = (string)$i;
			}
			if($page_number == 2){
			$link_arr[$i+1] = (string)($i+1);
			}
			}
			}
			else{
			
			for($i = ($total_pages-$config['link_limit']);$i<(($total_pages-$config['link_limit'])+$config['link_limit']);$i++){
			$link_arr[$i] = (string)$i;
			}
			} 
			*/
			$link_arr['next'] = '>';
			//$link_arr[] = $total_pages;
			$link_arr['last'] = 'last';
		}
		$data = $link_arr;
		//$last_key = key( array_slice( $data, -1, 1, TRUE ) );
		$last_key = $total_pages;
		$el = "";
		$el .= $config['start_tag'];
		foreach ($data as $key => $value) {
			if ($key == $page_number) {
				$el .=  str_replace(['{value}'], [$value], $config['active_link']);
			} else if ($key == 'prev') {
				$el .=  str_replace(['{link}', '{value}'], [((1 >= $page_number) ? $page_number : ($page_number - 1)), $value], $config['link']);
			} else if ($key == 'next') {
				$el .=  str_replace(['{link}', '{value}'], [((!($last_key > $page_number)) ? $page_number : ($page_number + 1)), $value], $config['link']);
			} else if ($key == 'first') {
				$el .=  str_replace(['{link}', '{value}'], [1, 'first'], $config['link']);
			} else if ($key == 'last') {
				$el .=  str_replace(['{link}', '{value}'], [$last_key, 'last'], $config['link']);
			} else {
				$el .=  str_replace(['{link}', '{value}'], [trim($key), $value], $config['link']);
			}
		};
		$el .= $config['end_tag'];
		echo $el;
	}
}
