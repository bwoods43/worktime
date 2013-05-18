<?php
class Project extends UB_Model {
	
	public $_table = 'projects';
	
	public static function getProjects( $params = array() ) {
		extract($params);
		$db = UB_Db::getInstance();
		// 2/26/11 baw - always need office id
		$where = ' WHERE 1=1 AND office_id = ' . $_SESSION['beynon_office_id'];
		$tmp = new Project();
		$fields = $tmp->getFields();
		foreach ( $fields as $field ) {
			if ( !is_null(${$field}) ) {
				$where .= " AND {$field} = '" . $db->escape(${$field}) . "' ";
			}
		}

		// 8/1/11 baw - do not show archived projects if not needed
		if ($_GET['archived'] == 1) {
			$archived = ' AND archived = 1 ';
		} else {
			$archived = ' AND archived = 0 ';
		}

		$sql = "SELECT * FROM projects $where $archived ORDER BY project_name ASC, created DESC";

		return $db->get_results($sql);
	}
	
	public function delete() {
		$ID = $this->_db->escape($this->{$this->_primary_key});
		$sql = array(
			"DELETE FROM projects_users WHERE project_id = '$ID'",
			"DELETE FROM projects_tasks WHERE project_id = '$ID'",
			"DELETE FROM projects WHERE ID = '$ID'"
		);
		foreach ( $sql as $q ) {
			$this->_db->query($q);
		}
	}
	
	public function setEmployees( $IDs ) {
		$project_id = $this->_db->escape($this->{$this->_primary_key});
		$this->_db->query("DELETE FROM projects_users WHERE project_id = '{$project_id}'");
		if ( is_array($IDs) && count($IDs) > 0 && $this->{$this->_primary_key} ) {
			$sql = "INSERT INTO projects_users (project_id, user_id) VALUES ";
			foreach ( $IDs as $ID ) {
				$ID = $this->_db->escape($ID);
				$sql .= "('{$project_id}', '{$ID}'), ";
			}
			$sql = substr($sql, 0, -2) . ';';
			$this->_db->query($sql);
		}
	}
	
	public function setTasks( $IDs ) {
	
		// 7/3/10 baw - mandatory tasks for all projects (5 = rain, 10 = drive)
		$mandatory_tasks = array(5,10);
		foreach ($mandatory_tasks as $v) {
			if (!in_array($v, $IDs)) {
				$IDs[] = $v;
			}
		}

		if ( is_array($IDs) && count($IDs) > 0 && $this->{$this->_primary_key} ) {
			$project_id = $this->_db->escape($this->{$this->_primary_key});
			$sql = "INSERT INTO projects_tasks (project_id, task_id) VALUES ";
			foreach ( $IDs as $ID ) {
				$ID = $this->_db->escape($ID);
				$sql .= "('{$project_id}', '{$ID}'), ";
			}
			$sql = substr($sql, 0, -2) . ';';
		}
		$this->_db->query("DELETE FROM projects_tasks WHERE project_id = '{$project_id}'");
		$this->_db->query($sql);
	}
	
	public function getEmployees() {
		$ID = $this->_db->escape($this->{$this->_primary_key});
		return $this->_db->get_col("SELECT p.user_id FROM projects_users p, users u WHERE p.project_id = '{$ID}' AND u.ID=p.user_id AND u.hidden != 1 ORDER BY u.last_name");
	}
	
	public function getTasks() {
		$ID = $this->_db->escape($this->{$this->_primary_key});
		// 02/12/13 baw include office check for tasks
		return $this->_db->get_col("SELECT pt.task_id FROM projects_tasks pt, tasks t WHERE pt.project_id = '{$ID}' AND pt.task_id = t.ID AND t.office_id = " . $_SESSION['beynon_office_id'] . " ORDER BY t.task_name");
	}

	public function getActualHours() {
		return $this->_db->get_var("SELECT SUM(hours) FROM project_time WHERE project_id = '{$this->ID}'");
	}
	
	public function getTaskHours( $task_id ) {
		$task_id = $this->_db->escape($task_id);
		return (int) $this->_db->get_var("SELECT SUM(hours) FROM project_time WHERE project_id = '{$this->ID}' AND task_id = '{$task_id}';");
	}
	
	public function getNote( $date ) {
		return Project_note::getNote( $this->ID, $date );
	}
	
}