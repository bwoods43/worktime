<?php
class Project_time extends UB_Model {
	
	public $_table = 'project_time';

	public static function getEntries( $params = array() ) {
		$db = UB_Db::getInstance();
		extract($params);
		if ( $work_date ) {
			$work_date = date("Ymd", strtotime($work_date));
		}
		$sql = "
			SELECT pt.*, u.last_name, u.first_name, t.task_name
			FROM project_time pt, users u, tasks t
			WHERE pt.user_id = u.ID AND pt.task_id = t.ID
		";
		$me = new Project_time();
		foreach ( $me->_fields as $field ) {
			if ( !empty(${$field}) ) {
				$sql .= " AND $field = '" . $db->escape(${$field}) . "' ";
			}
		}
		$sql .= ' ORDER BY u.last_name ASC, u.first_name ASC, t.task_name ASC';
		return $db->get_results($sql);
	}

	public function delete() {
		$vars = $this->getArray();
		unset($vars['ID']);
		unset($vars['hours']);
		unset($vars['per_diem']);
		unset($vars['manager_id']);
		$sql = "DELETE FROM {$this->_table} WHERE ";
		foreach ( $vars as $k => $v ) {
			$sql .= "$k = '" . $this->_db->escape($v) . "' AND ";
		}
		$sql = substr($sql, 0, -4);
		$this->_db->query($sql);
		if ( is_null($this->_db->last_error) ) {
			return true;
		}
		$this->_last_error = $this->_db->last_error;
		return false;
	}
	
}