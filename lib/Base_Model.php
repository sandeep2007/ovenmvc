<?php

use Evolution\CodeIgniterDB as CI;

abstract class Base_model
{
	protected $db;
	public $table;
	public $primary_key = 'id';
	protected $msg;
	public $blacklist = [];
	public $debug = FALSE;
	public $debug_mode = TRUE;
	public $db_error;
	public $db_debug;
	protected $_data;
	protected $_message;
	protected $_error;
	protected $_status;
	protected $_count;
	protected $_total_count;
	protected $_id;
	public $config;

	public function __construct()
	{

		$this->config = getConfig();

		$db_config = array(
			'dsn'	=> '',
			'hostname' => $this->config['db']['hostname'],
			'username' => $this->config['db']['username'],
			'password' => $this->config['db']['password'],
			'database' => $this->config['db']['database'],
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => TRUE
		);

		$this->db = &CI\DB($db_config);
		// unset($this->config['db']);

		if (isset($this->config['debug_mode'])) {
			$this->debug_mode = $this->config['debug_mode'];
		}
		$this->db_debug = $this->db->db_debug;
		if ($this->debug_mode == TRUE) {
			$this->db->db_debug = FALSE;
		}
		if ($this->debug_mode == FALSE) {
			$this->db->db_debug = $this->db_debug;
		}
	}

	protected function response($status, $message = NULL, $data = NULL, $error = NULL)
	{
		$result = array('status' => $status, 'message' => $message, 'data' => $data, 'error' => $error);
		$this->_data = $data;
		$this->_error = $error;
		$this->_message = $message;
		$this->_status = $status;
		$this->_count = (@$data['rows']) ? count($data['rows']) : 0;
		$this->_total_count = $data['total_count'];
		return $this;
	}
	protected function queryResult()
	{
		$error = $this->db->error();
		if ($this->debug_mode == TRUE && $this->config['rest_debug'] == TRUE) {
			$this->db_error = array('message' => $error['message'], 'error' => $error['code']);
		} else if ($this->config['rest_debug'] == FALSE) {
			$this->db_error = array('message' => "DATABASE_ERROR", 'error' => $error['code']);
		}
		if ($this->debug_mode == FALSE) {
			$this->db->db_debug = $this->db_debug;
		}
		if ($error['code'] == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function check_column_exist($column, $value)
	{
		$query = $this->db->get_where($this->table, array($column => $value));
		if (empty(@$query->row_array())) {
			return false;
		} else {
			return true;
		}
	}
	public function create(array $param)
	{
		$data = (isset($param['data'])) ? $param['data'] : NULL;
		if ($this->debug === TRUE) {
			echo $this->db->get_compiled_select($this->table);
			die;
		}
		$query = $this->db->insert($this->table, $data);
		$query_validator = $this->queryResult();
		if ($query_validator === TRUE) {
			$this->_id = $this->db->insert_id();
			return $this->response($query_validator, 'SUCCESSFULLY_INSERTED', NULL, NULL);
		} else {
			return $this->response($query_validator, 'ERROR_OCCURRED', NULL, $this->db_error);
		}
	}
	public function update(array $param = array())
	{
		$id = (isset($param['id'])) ? $param['id'] : NULL;
		$limit = (isset($param['limit'])) ? $param['limit'] : NULL;
		$offset = (isset($param['offset'])) ? $param['offset'] : NULL;
		$search_rules = (isset($param['like'])) ? $param['like'] : NULL;
		$where = (isset($param['where'])) ? $param['where'] : NULL;
		$data = (isset($param['data'])) ? $param['data'] : NULL;
		$this->_id = $id;
		if (gettype($where) == 'array') {
			if (count($where)) {
				foreach ($where as $wh) {
					$this->db->where($wh['key'], "'" . $wh['value'] . "'", FALSE);
				}
			}
		} else if (gettype($where) == 'string') {
			$this->db->where($where, NULL, FALSE);
		} else {
			$this->db->where($this->primary_key, "'" . $id . "'", FALSE);
		}
		if ($this->debug === TRUE) {
			echo $this->db->get_compiled_select($this->table);
			die;
		}
		$query = $this->db->update($this->table, $data);
		$query_validator = $this->queryResult();
		if ($query_validator === TRUE) {
			if ($this->db->affected_rows() > 0) {
				$message = "SUCCESSFULLY_UPDATED";
				return $this->response($query_validator, $message, NULL, NULL);
			} else {
				$message = "NOT_UPDATED";
				return $this->response(FALSE, $message, $this->db->affected_rows(), NULL);
			}
		} else {
			return $this->response(FALSE, 'RECORD_NOT_FOUND', NULL, $this->db_error);
		}
	}
	public function delete(array $param = array())
	{
		$id = (isset($param['id'])) ? $param['id'] : NULL;
		$limit = (isset($param['limit'])) ? $param['limit'] : NULL;
		$offset = (isset($param['offset'])) ? $param['offset'] : NULL;
		$search_rules = (isset($param['like'])) ? $param['like'] : NULL;
		$where = (isset($param['where'])) ? $param['where'] : NULL;
		$this->_id = $id;
		if (gettype($where) == 'array') {
			if (count($where)) {
				foreach ($where as $wh) {
					$this->db->where($wh['key'], "'" . $wh['value'] . "'", FALSE);
				}
			}
		} else if (gettype($where) == 'string') {
			$this->db->where($where, NULL, FALSE);
		} else {
			$this->db->where($this->primary_key, "'" . $id . "'", FALSE);
		}
		if ($this->debug === TRUE) {
			echo $this->db->get_compiled_select($this->table);
			die;
		}
		$query = $this->db->delete($this->table);
		$query_validator = $this->queryResult();
		if ($query_validator === TRUE) {
			return $this->response($query_validator, 'RECORD_DELETED', $query, NULL);
		} else {
			return $this->response($query_validator, 'RECORD_NOT_FOUND', NULL, $this->db_error);
		}
	}
	public function get(array $param = array())
	{
		$id = (isset($param['id'])) ? $param['id'] : NULL;
		$limit = (isset($param['limit'])) ? $param['limit'] : NULL;
		$offset = (isset($param['offset'])) ? $param['offset'] : NULL;
		$search_rules = (isset($param['like'])) ? $param['like'] : NULL;
		$where = (isset($param['where'])) ? $param['where'] : NULL;
		$select = (isset($param['select'])) ? $param['select'] : NULL;
		$orders = (isset($param['order'])) ? $param['order'] : NULL;
		$this->_id = $id;
		if ($this->db->table_exists($this->table) === FALSE) {
			$this->db->get($this->table);
			$this->queryResult();
			return $this->response(FALSE, 'TABLE_NOT_FOUND', NULL, $this->db_error);
		}
		if (gettype($where) == 'array') {
			if (count($where)) {
				foreach ($where as $wh) {
					if (gettype($wh) == 'string') {
						$this->db->where($wh, NULL, FALSE);
					} else {
						if (isset($wh['condition']) && ($wh['condition'] == 'OR' || $wh['condition'] == 'or')) {
							$this->db->or_where($wh['key'], "'" . $wh['value'] . "'", FALSE);
						} else {
							$this->db->where($wh['key'], "'" . $wh['value'] . "'", FALSE);
						}
					}
				}
			}
		} else if (gettype($where) == 'string') {
			$this->db->where($where, NULL, FALSE);
		}
		if (isset($search_rules['rules']) && isset($search_rules['key']) && !empty(trim($search_rules['key']))) {
			$this->db->group_start();
			if (isset($search_rules['rules'][0])) {
				$x = 0;
				foreach ($search_rules['rules'] as $rl) {
					if ($x == 0) {
						$this->db->like($rl['key'], $search_rules['key'], $rl['value'], FALSE);
					} else {
						$this->db->or_like($rl['key'], $search_rules['key'], $rl['value'], FALSE);
					}
					$x++;
				}
			} else {
				foreach ($search_rules['rules'] as $column => $method) {
					$this->db->or_like($column, $search_rules['key'], $method, FALSE);
				}
			}
			$this->db->group_end();
		}
		// $total_count = $this->db->get($this->table);
		// if ($total_count) {
		// 	$total_count = $total_count->num_rows();
		// }
		$total_count = $this->db->select("COUNT(*) as total_count")->get($this->table)->row()->total_count;
		/* if(count($where)){ foreach($where as $wh){ $this->db->where($wh['key'], "'".$wh['value']."'", FALSE); } } */
		if (gettype($where) == 'array') {
			if (count($where)) {
				foreach ($where as $wh) {
					if (gettype($wh) == 'string') {
						$this->db->where($wh, NULL, FALSE);
					} else {
						if (isset($wh['condition']) && ($wh['condition'] == 'OR' || $wh['condition'] == 'or')) {
							$this->db->or_where($wh['key'], "'" . $wh['value'] . "'", FALSE);
						} else {
							$this->db->where($wh['key'], "'" . $wh['value'] . "'", FALSE);
						}
					}
				}
			}
		} else if (gettype($where) == 'string') {
			$this->db->where($where, NULL, FALSE);
		}
		if (gettype($select) == 'array') {
			$column_lists = array_unique($select);
			if (count($this->blacklist)) {
				$column_lists = array_diff(array_unique($select), $this->blacklist);
				if (empty($column_lists)) {
					$column_lists = ['NULL'];
				}
			}
			$this->db->select($column_lists);
		} else if (gettype($select) == 'string') {
			$this->db->select($select, FALSE);
		} else {
			if (count($this->blacklist)) {
				if ($total_count) {
					$db_columns = $this->db->list_fields($this->table);
					$column_lists = array_diff($db_columns, $this->blacklist);
					$this->db->select($column_lists);
				}
			}
		}
		if (isset($search_rules['rules']) && isset($search_rules['key']) && !empty(trim($search_rules['key']))) {
			$this->db->group_start();
			if (isset($search_rules['rules'][0])) {
				$x = 0;
				foreach ($search_rules['rules'] as $rl) {
					if ($x == 0) {
						$this->db->like($rl['key'], $search_rules['key'], $rl['value'], FALSE);
					} else {
						$this->db->or_like($rl['key'], $search_rules['key'], $rl['value'], FALSE);
					}
					$x++;
				}
			} else {
				foreach ($search_rules['rules'] as $column => $method) {
					$this->db->or_like($column, $search_rules['key'], $method, FALSE);
				}
			}
			$this->db->group_end();
		}
		if (gettype($orders) == 'array') {
			if ($orders) {
				foreach ($orders as $order) {
					$this->db->order_by($order['key'], $order['value']);
				}
			}
		} else if (gettype($orders) == 'string') {
			$this->db->order_by($orders);
		} else {
			$this->db->order_by($this->primary_key, 'DESC');
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		if (!empty(trim($id))) {
			$this->db->where($this->primary_key, "'" . $id . "'", FALSE);
		}
		if ($this->debug === TRUE) {
			echo $this->db->get_compiled_select($this->table);
			die;
		}
		$query = $this->db->get($this->table);
		$query_validator = $this->queryResult();
		if ($query_validator === TRUE) {
			if (count($query->result_array())) {
				$data = array('rows' => $query->result_array(), 'total_count' => $total_count);
				return $this->response($query_validator, 'RECORD_FETCHED', $data, NULL);
			} else {
				$data = array('rows' => NULL, 'total_count' => 0);
				return $this->response(FALSE, 'RECORD_NOT_FOUND', $data, NULL);
			}
		} else {
			return $this->response($query_validator, 'ERROR_OCCURRED', NULL, $this->db_error);
		}
	}
	public function query($sql)
	{
		$qstr = explode(" ", $sql);
		if (isset($qstr[1])) {
			$fw = $qstr[0];
		} else {
			$fw = "INVALID";
		}
		$query_type = $fw;
		if ($this->debug === TRUE) {
			echo $sql;
			die;
		}
		$query = $this->db->query($sql);
		$query_validator = $this->queryResult();
		if ($query_validator === FALSE) {
			return $this->response($query_validator, 'INVALID_QUERY', NULL, $this->db_error);
		} else if (($query_type == 'select' || $query_type == 'SELECT') && count(@$query->result_array())) {
			$data = array('rows' => $query->result_array(), 'total_count' => count($query->result_array()));
			return $this->response($query_validator, 'RECORD_FETCHED', $data, NULL);
		} else if (($query_type == 'delete' || $query_type == 'DELETE') && $query === TRUE) {
			return $this->response($query_validator, 'RECORD_DELETED', $query, NULL);
		} else if (($query_type == 'update' || $query_type == 'UPDATE') && $query === TRUE) {
			return $this->response($query_validator, 'RECORD_UPDATED', $this->db->affected_rows(), NULL);
		} else if (($query_type == 'insert' || $query_type == 'INSERT') && $query === TRUE) {
			return $this->response($query_validator, 'RECORD_INSERTED', $query, NULL);
		} else if (($query_type == 'call' || $query_type == 'CALL') && $query === TRUE) {
			return $this->response($query_validator, 'SUCCESSFULLY_CALLED', $query, NULL);
		} else {
			return $this->response($query_validator, 'ERROR_OCCURRED', NULL, $this->db_error);
		}
	}
	public function data($index = NULL)
	{
		if ($index === NULL) {
			return $this->_data['rows'];
		}
		return $this->_data['rows'][$index];
	}
	public function error()
	{
		return $this->_error;
	}
	public function error_code()
	{
		return $this->_error['error'];
	}
	public function error_message()
	{
		return $this->_error['message'];
	}
	public function message()
	{
		return $this->_message;
	}
	public function status()
	{
		return $this->_status;
	}
	public function count()
	{
		return $this->_count;
	}
	public function total_count()
	{
		return $this->_total_count;
	}
	public function trans_start()
	{
		$this->db->trans_start();
	}
	public function trans_complete()
	{
		$this->db->trans_complete();
	}
	public function trans_status()
	{
		return $this->db->trans_status();
	}
	public function trans_off()
	{
		$this->db->trans_off();
	}
	public function trans_begin()
	{
		$this->db->trans_begin();
	}
	public function trans_rollback()
	{
		$this->db->trans_rollback();
	}
	public function trans_commit()
	{
		$this->db->trans_commit();
	}
	public function id()
	{
		return $this->_id;
	}
	public function get_query($flag = NULL)
	{
		if ($flag == NULL) {
			return $this->db->last_query();
		} else if ($flag == 'LOG') {
			logger('message', $this->db->last_query());
		}
	}
	public function field_validator($table, $data)
	{
		return array_intersect_key($data, array_flip($this->db->list_fields($table)));
	}
	public function check_table($table)
	{
		return $this->db->table_exists($table);
	}
	public function check_field($field, $table)
	{
		return $this->db->field_exists($field, $table);
	}
}
