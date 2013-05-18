<?php

/**
 * Utility Belt Model 
 * 
 * Assumes primary key is ID
 * Can set restricted fields via constructor
 * 
 * @author theandystratton
 *
 */
class UB_Model {
	
	public $_primary_key = 'ID';
	public $_table;
	protected $_fields = array();
	protected $_restricted = array('ID');
	protected $_description;
	public $_db;
	protected $_last_error;
	
	public function __construct( $key = null ) {
		$this->setup();
		if ( !is_null($key) ) {
			$this->load($key);
		}
	}

	protected function setup() {
		$this->_db = UB_Db::getInstance();
		if ( is_null($this->_table) ) {
			$this->_table = $this->_db->escape(strtolower(get_class($this)));
		}
		$fields = $this->_db->get_results("DESCRIBE " . $this->_table . ";");
		if ( count($fields) > 0 ) {
			$this->_description = $fields;
			foreach ( $fields as $col ) {
				if ( !in_array($col->Field, $this->_restricted) ) $this->_fields[] = $col->Field;
			}
		}
	}
	
	/**
	 * Load data into model from database using primary key
	 * 
	 * @param mixed $key
	 * @return void
	 */
	public function load( $key ) {
		$key = $this->_db->escape($key);
		$array = $this->_db->get_row("SELECT * FROM {$this->_table} WHERE {$this->_primary_key} = '$key'", ARRAY_A);
		$this->{$this->_primary_key} = $array[$this->_primary_key];
		$this->loadArray($array);
	}
	
	public function loadArray( $array ) {
		if ( is_array($array) ) {
			foreach ( $array as $field => $val ) {
				if ( in_array($field, $this->_fields) ) {
					$this->{$field} = $val;
				}
			}
		}
	}

	public function save() {
		if ( !is_null($this->{$this->_primary_key}) ) {
			$sql = "UPDATE {$this->_table} SET ";
			$ID = $this->{$this->_primary_key};
			foreach ( $this->_fields as $field ) {
				$val = $this->_db->escape($this->{$field});
				$sql .= "{$field} = '{$val}', ";
			}
			$sql = substr($sql, 0, -2) . " WHERE {$this->_primary_key} = '{$ID}';";
		 	$this->_db->query($sql);
		}
		else {
			$sql = "INSERT INTO {$this->_table} (" . implode(', ', $this->_fields) . ") VALUES (";
			foreach ( $this->_fields as $field ) {
				$val = $this->_db->escape($this->{$field});
				$sql .= "'{$val}', ";
			}
			$sql = substr($sql, 0, -2) . ");";
			 $this->_db->query($sql);
			 $this->{$this->_primary_key} = $this->_db->get_var("SELECT LAST_INSERT_ID();");
		}
		if ( !is_null($this->_db->last_error) ) {
			$this->_last_error = $this->_db->last_error;
			return false;
		}
		else return true;
	}

	public function delete() {
		if ( !is_null($this->{$this->_primary_key}) ) {
			$ID = $this->_db->escape($this->{$this->_primary_key});
			$this->_db->query("DELETE FROM {$this->_table} WHERE {$this->_primary_key} = '$ID';");
			if ( is_null($this->_db->last_error) ) {
				return true;
			}
			$this->_last_error = $this->_db->last_error;
		}
		return false;
	}
	
	public function getLastError() {
		return $this->_last_error;
	}

	public function getPrimaryKey() {
		return $this->{$this->_primary_key};
	}
	
	public function getID() {
		$this->getPrimaryKey();
	}
	
	public function getFields() {
		return $this->_fields;
	}

	public function getArray() {
		$return = array();
		foreach ( $this->_fields as $field ) $return[$field] = $this->{$field};
		$return[$this->_primary_key] = $this->{$this->_primary_key};
		return $return;
	}

	public function bulk_delete( $ids ) {
		$db = $this->_db;
		if ( !is_array($ids) ) {
			$ids = array($ids);
		}
		foreach ( $ids as $i => $id ) $ids[$i] = $db->escape($id);
		$ids = implode("','", $ids);
		$sql = "DELETE FROM {$this->_table} WHERE {$this->_primary_key} IN ('{$ids}')";
		$db->query( $sql );
	}

	public function find( $args ) {
		$final_args = array();
		foreach ( $this->_fields as $field ) {
			if ( !empty($args[$field]) ) {
				$final_args[$field] = $this->_db->escape($args[$field]);
			}
		}
		$sql = "SELECT * FROM {$this->_table} WHERE 1=1";
		if ( count($final_args) ) {
			foreach ( $final_args as $field => $val ) {
				$sql .= " AND {$field} = '{$val}' "; 
			}
		}
		$sql .= " LIMIT 1;";
		$data = $this->_db->get_row($sql, ARRAY_A);
		$this->loadArray($data);
	}
	
}