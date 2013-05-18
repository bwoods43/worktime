<?php
class Task extends UB_Model {
	public $_table = 'tasks';
	
	public static function getTasks( $params = array() ) {
		$db = UB_Db::getInstance();
		extract($params);
		// 02/02/13 baw show tasks by office and global tasks
		$sql = 'SELECT * FROM tasks WHERE office_id = ' . $_SESSION['beynon_office_id'] . ' OR office_id = 99 ORDER BY task_name ASC';
		return $db->get_results($sql);
	}

// 02/09/13 baw removing task deletion functionality
/*
public function delete() {
		$ID = $this->_db->escape($this->{$this->_primary_key});
		// kill project link
		$this->_db->query("DELETE FROM projects_tasks WHERE task_id = '$ID';");
		// kill time entries for this task
		$this->_db->query("DELETE FROM project_time WHERE task_id = '$ID';");
		// kill task
		$this->_db->query("DELETE FROM {$this->_table} WHERE {$this->_primary_key} = '$ID';");
	}
*/	
}