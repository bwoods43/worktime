<?php
class Project_note {
	
	public static function getNote( $project_id, $work_date ) {
		$db = UB_Db::getInstance();
		$project_id = intval($project_id);
		if ( !is_numeric($work_date) ) {
			$work_date = strtotime($work_date);
		}
		$work_date = date("Y-m-d", $work_date);
		$sql = "SELECT note FROM project_notes WHERE project_id = '$project_id' AND work_date = '$work_date';";
		return $db->get_var($sql);
	}
	public static function setNote( $project_id, $work_date, $note ) {
		$db = UB_Db::getInstance();
		$project_id = $db->escape($project_id);
		if ( !is_numeric($work_date) ) {
			$work_date = strtotime($work_date);
		}
		$work_date = $db->escape(date("Ymd", $work_date));
		$note = $db->escape($note);
		$db->query("INSERT INTO project_notes (project_id, work_date, note) VALUES ('$project_id', '$work_date', '$note') ON DUPLICATE KEY UPDATE note = '$note';");
	}
	
}