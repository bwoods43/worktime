<?php
class Admin_reports_Controller extends Authrequired_Controller {

	public $auth_type_required = 'admin';
	
	public function index() {
		$action = $this->request->getPiece(2);
		if ( $action != '' ) {
			$this->{$action}();
			return true;
		}
		$this->view->projects = Project::getProjects();
		$this->view->employees = User::getUsers(array(
			'account_type' => 'employee'
		));
		echo $this->render('/admin/reports/index');
	}
	
	public function overview() {
		$this->view->projects = Project::getProjects();
		$this->view->project = new Project($_GET['project_id']);
		echo $this->render('/admin/reports/overview');
	}
		
	// 7/3/10 baw - update function to create timesheet
	public function timesheet($report_type="by-employee") {
	
		/* 8/24/10 baw - so far, we have two possible reports. we're listing the time by people and then project ($report_type = by-employee). we are also listing by project then people ($report_type = by-project). to account for this within one function, we will define the top level as "parent" and second level as "child." Therefore, the person's name and the project name will be either parent or child in the following script
		NOTE: by-employee is the default report because that was the first one we created */
		
		// just need to look for the report type
		if ($_GET['report_type'] || $_POST['report_type'] === 'by-project') {
			$report_type = 'by-project';
		}

		/* 9/5/10 baw - this is a simple function to 'verify' the time listed on the timesheet. it allows admins the option to verify and have a username/timestamp on the report. we are not keeping track of the time for every task added, so if a timesheet is verified, and new time is added at a later date, the admin will need to verify again */
		function verifyTime($year, $week, $status="current") {		
			$db = UB_Db::getInstance();

			if ($status == "new") {			
				// first, we'll delete a timestamp if it exists
				$sql = "
					DELETE FROM timesheet_info WHERE week = " . $week . " AND year = " . $year . " LIMIT 1";
				$db->query( $sql );	
			
				$sql = "
					INSERT INTO timesheet_info (user_id, year, week, verify_date, office_id) VALUES (
					'" . User::getCurrentUser()->ID . "',
					'" . $year . "', 
					'" . $week . "',
					'" . date('Y-m-d H:i:s') . "',
					'" . $_SESSION['beynon_office_id'] . "')";
				$db->query( $sql );
			}
			
			// determine if time has been verified
			$sql = "
				SELECT u.first_name, u.last_name, DATE_FORMAT(ti.verify_date, '%l:%i %p on %W, %M %d, %Y') as verify_date FROM timesheet_info ti, users u WHERE u.ID = ti.user_id AND ti.week = " . $week . " AND ti.year = " . $year . " AND ti.office_id = " . $_SESSION['beynon_office_id'] . " LIMIT 1";
				return $db->get_results( $sql );
		}
		
		function week2date($year, $week, $day = 1) {
    	// use "strtotime('2007W011')" for Monday "1", of Week 1 ("01") of year 2007 ("2007")
    	$string = $year . 'W' . sprintf("%02d", $week) . $day;
    	return strtotime($string);
		}

		// based on date, select start and end dates for the week
		if ($_POST['work_date']) {
			$use_date = $_POST['work_date'];
		} else {
			if ($_GET['work_date']) { 
				$use_date = $_GET['work_date'];			
			} else {
				$use_date = date("m/d/Y");
			}
		}

		// get week
		$use_date = explode("/", $use_date);
		$week = date("W", mktime(0, 0, 0, $use_date[0], $use_date[1], $use_date[2]));
		
		// call verify function here
		$verify_info = verifyTime($use_date[2], $week, ($_POST['verify_status'] ? 'new':'current'));
		
		// quick hack for the end of the year	- needs work for 2013!	
		if ($week == 52 && $use_date[1] == 1) {
			$calc_year = $use_date[2]-1;
		} else {
			$calc_year = $use_date[2];		
		}
		// END quick hack for the end of the year		
		$start = date('Y-m-d', week2date($calc_year, $week, 1));
		$end = date('Y-m-d', week2date($calc_year, $week, 7));
		
		// change sort depending on the type of report, by-employee or by-project
		switch ($report_type) {
			case "by-project":
				$sort = " p.project_name ASC, u.last_name ASC, u.first_name ASC, ";
				break;
			default: //by-employee
				$sort = " u.last_name ASC, u.first_name ASC, p.project_name ASC, ";
				break;
		}
		
		$db = UB_Db::getInstance();
		$sql = "
			SELECT	pt.*, u.first_name, u.last_name, p.project_name  
			FROM 	projects p, users u, project_time pt
			WHERE	pt.project_id = p.ID 
					AND u.ID = pt.user_id
					AND work_date BETWEEN '{$start}' AND '{$end}' 
					AND p.office_id = " . $_SESSION['beynon_office_id'] . " 
			ORDER BY " . $sort . " work_date ASC 
		";
		$entries = $db->get_results( $sql );
		
		// new array for organized data
		$data = array();
		
		if (!$entries) {
			$error_msg[] = "There are no entries during the period of time you have selected. Please select a different date and try again.<br />";			
		} else {
		
		foreach ($entries as $entry) {
		
			// define variables depending on report type
			switch ($report_type) {
				case "by-project":
					$parent_value = $entry->project_name;
					$parent_id = $entry->project_id;
					$child_value = $entry->first_name . " " . $entry->last_name;
					$child_id = $entry->user_id;
					$per_diem = $entry->per_diem;
					break;
				default: //by-employee
					$parent_value = $entry->first_name . " " . $entry->last_name;
					$parent_id = $entry->user_id;
					$child_value = $entry->project_name;
					$child_id = $entry->project_id;
					$per_diem = $entry->per_diem;
					break;
			}
		
			// parent info
			if ($parent_id != $current_parent_id) {			
				$data[$parent_id] = array(
					'parent' => $parent_value 
				);
				$current_parent_id = $parent_id;
				// reset child ID
				$current_child_id = '';
			}	
			
			// child info
			if ($child_id != $current_child_id) {			
				$data[$parent_id]['children'][$child_id] = array(
					'child' => $child_value
				);
				
				$start_date = $start;
				$end_date = $end;
				
				$check_date = $start_date;
				while ($check_date <= $end_date) {
					$data[$parent_id]['children'][$child_id]['days'][$check_date] = array(
						'regular' => 0, 
						'drive' => 0,
						'rain' => 0, 
						'per diem' => 0
					);
    			$check_date = date ("Y-m-d", strtotime ("+1 day", strtotime($check_date)));
				}
				$current_child_id = $child_id;
			}

// 02/11/13 baw account for tasks for different offices
			switch ($_SESSION['beynon_office_id']) {
				case 1:
					$rain_day_task = 5;
					$drive_task = 10;
				break;
				case 2:
					$rain_day_task = 205;
					$drive_task = 2010;				
				break;
			}
				
			// add regular hours
			if ($entry->task_id != $rain_day_task && $entry->task_id != $drive_task) {
				$data[$parent_id]['children'][$child_id]['days'][$entry->work_date]['regular'] += $entry->hours;
			}

			// add rain hours
			if ($entry->task_id == $rain_day_task) {
				$data[$parent_id]['children'][$child_id]['days'][$entry->work_date]['rain'] += $entry->hours;
			}

			// add drive hours
			if ($entry->task_id == $drive_task) {
				$data[$parent_id]['children'][$child_id]['days'][$entry->work_date]['drive'] += $entry->hours;
			}
			
			// count per diem
			if ($entry->per_diem || $entry->work_date< '2010-12-10') {
				$data[$parent_id]['children'][$child_id]['days'][$entry->work_date]['per diem'] = 1;
				$data[$parent_id]['per diem'][$entry->work_date] = 1;	
			}
		}
		
		
		}

/*
echo "<PRE>";
print_r($data);
exit;
*/
		
		if ($error_msg) {
				$this->view->start = $start;
				$this->view->end = $end;
				$this->view->errors = $error_msg;		
				$this->view->projects = Project::getProjects();
				echo $this->render('/admin/reports/timesheet');
		} else {
		
		switch ( $_GET['type'] ) {
			case 'csv':
				
				break;
			default:
				$this->view->user = '';
				$this->view->entries = $data;
				$this->view->verify_info = $verify_info[0];
				$this->view->start = $start;
				$this->view->end = $end;
				$this->view->projects = Project::getProjects();
				echo $this->render('/admin/reports/timesheet');
				break;
		}
		
		}
		
	}
	
} 