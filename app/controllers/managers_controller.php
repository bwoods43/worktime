<?php
class Managers_Controller extends Authrequired_Controller {
	
	public $auth_type_required = User::TYPE_MANAGER;
	
	public function index() {
		if ( sizeof($_POST) ) {
			if ( $_POST['action'] == 'Enter Time' ) {
				$url = '/managers/add/' . $_POST['project_id'] .'/?date=' . $_POST['work_date'];
			}
			else {
				$url = '/managers/review/' . $_POST['project_id'] .'/?date=' . $_POST['work_date'];
			}
			$this->request->redirect( $url );
		}
		$this->view->user = User::getCurrentUser();
/*
// 7/2/10 baw - instead of showing only projects assigned to manager, show all projects
		$this->view->projects = Project::getProjects(
			User::isAdmin() ? array() : array('manager_id' => User::getCurrentUser()->ID)
		);
*/
		$this->view->projects = Project::getProjects(array());
		echo $this->render('/managers/index');
	}

	public function ajax() {
		switch ( $_GET['action'] ) {
			
			case 'set_note':
				$find = array();
				parse_str($_SERVER['QUERY_STRING'], $find);
				Project_note::setNote($find['project_id'], $find['work_date'], $find['note']);
				$response = new stdClass();
				$response->status = true;
				$response->message = 'Note saved.';
				echo json_encode($response);
				break;
			
			case 'delete_entry':
				$response = new stdClass();
				// prep search array
				$find = array();
				parse_str($_SERVER['QUERY_STRING'], $find);
				unset($find['hours']);
				$time = new Project_time();
				$find['work_date'] = date("Y-m-d", strtotime($find['work_date']));
				$time->find($find);
				if ( $time->delete() ) {
					$response->status = true;
					$response->message = 'Your time entry has been deleted.';
					$response->row = $_GET['row'];
				}
				else {
					$response->status = false;
					$response->message = 'There was an error updating your time entry.';
				}
				echo json_encode($response);
				break;
			
			case 'update_entry':
				$response = new stdClass();
				// prep search array
				$find = array();
				parse_str($_SERVER['QUERY_STRING'], $find);
				$find['user_id'] = $find['prev_user_id'];
				$find['task_id'] = $find['prev_task_id'];
				unset($find['hours']);
				unset($find['per_diem']);
				$time = new Project_time();
				$find['work_date'] = date("Y-m-d", strtotime($find['work_date']));
				$time->find($find);
				// 2010-12-12 BAW hours isn't the only thing being checked here
				if ( ($time->hours || $time->per_diem) && $time->delete() ) {
					$time->hours = floatval($_GET['hours']);
					$time->per_diem = $_GET['per_diem'];
					$time->user_id = floatval($_GET['user_id']);
					$time->task_id = floatval($_GET['task_id']);
					$time->manager_id = User::getCurrentUser()->ID;
					// project_id and date remain the same.
					$time->save();
					$response->status = true;
					$response->message = 'Your time entry has been updated.';
				}
				else {
					$response->status = false;
					$response->message = 'There was an error updating your time entry.';
				}
				echo json_encode($response);
				break;
			case 'entry_exists':
				$find = array();
				parse_str($_SERVER['QUERY_STRING'], $find);
				unset($find['action']);
				$time = new Project_time();
				$find['work_date'] = date("Y-m-d", strtotime($find['work_date']));
				$time->find($find);
				$response = new stdClass(); 
				if ( $time->hours && count($find) == 4 ) {
					unset($find['manager_id']);
					$response->status = true;
					$response->message = 'A time entry for this project and date already exists for this employee and task. <a href="/managers/review/'. $find['project_id'] . '?date=' . date("m/d/Y", strtotime($find['work_date'])) . '">Edit it &raquo;</a>';
				}
				else {
					$response->status = false;
					$response->message = "Time entry not found.";
				}
				echo json_encode($response);
				break;
			case 'employees':
				$ID = intval($_GET['project_id']);
				$project = new Project($ID);
				$employees = $project->getEmployees();
				$return = array();
				if ( count($employees) > 0 ) {
					foreach ( $employees as $user_id ) {
						$user = new User($user_id);
						$the_user = array(
							'ID' => $user_id,
							'name' => $user->last_name . ', ' . $user->first_name
						);
						$return[] = $the_user;
					}
				}
				echo json_encode($return);
				break;
				
			case 'tasks':
				$ID = intval($_GET['project_id']);
				$project = new Project($ID);
				$tasks = $project->getTasks();
				$return = array();
				if ( count($tasks) > 0 ) {
					foreach ( $tasks as $task_id ) {
						$task = new Task($task_id);
						$the_task = array(
							'ID' => $task_id,
							'name' => $task->task_name
						);
						$return[] = $the_task;
					}
				}
				echo json_encode($return);
				break;
		}
	}
	
	public function add() {
		
		$this->view->project = new Project($this->request->getPiece(2));
		$this->view->employees = $this->view->project->getEmployees();
		$this->view->tasks = $this->view->project->getTasks();
		$this->view->date = strtotime($_GET['date']);
		if ( !$this->view->date ) {
			$this->view->date = time();
		}
		
		$this->view->clean = array( 'note' => Project_note::getNote(
			$this->view->project->ID, date("Y-m-d", $this->view->date)
		));
		
		if ( sizeof($_POST) ) {
		
		// correct checkbox list
		//foreach($_POST['per_diem'] as $value) {
		//
		//}
		$corrected_per_diem = array();
		//echo end($_POST['per_diem']);
		for ($i = 1; $i <= end($_POST['per_diem']); $i++) {
			if (in_array($i, $_POST['per_diem'])) {
				$corrected_per_diem[$i-1] = 1;
			} else {
				$corrected_per_diem[$i-1] = 0;	
			}
		}
		$_POST['per_diem'] = $corrected_per_diem; 

			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			// loop through
			$this->view->errors = array();
			$this->view->error_msg = '';
			$entries = array();
			if ( count($this->view->clean['employees']) > 0 ) { 
				foreach ( $this->view->clean['employees'] as $k => $user_id ) {
					$entries[] = array(
						'project_id' => $this->view->clean['project_id'],
						'user_id' => $user_id,
						'task_id' => $this->view->clean['tasks'][$k],
						'hours' => floatval($this->view->clean['hours'][$k]),
						'per_diem' => $this->view->clean['per_diem'][$k],
						'work_date' => date("Ymd", strtotime($this->view->clean['date'])),
						'manager_id' => User::getCurrentUser()->ID
					);
					$this->view->errors[$k] = false;
					if ( !$user_id ) {
						$this->view->errors[$k] = true;
					}
					if ( !$this->view->clean['tasks'][$k] ) {
						$this->view->errors[$k] = true;
					}
					
					// 7/2/10 baw - if zero, convert to 0.0 to insert
					if ($_POST['hours'][0] == 0) $this->view->clean['hours'][$k] = "0.0";

					if ( !$this->view->clean['hours'][$k] || !is_numeric($this->view->clean['hours'][$k]) ) {
						$this->view->errors[$k] = true;
					}
					

					$time = new Project_time();
					$find = $entries[count($entries)-1];
					unset($find['hours']);
					$time->find($find);
					if ( $time->hours ) {
						unset($find['manager_id']);
						$this->view->errors[$k] = 'An entry for this employee already exists. <a href="/managers/review/' . $find['project_id'] . '/?date=' . date("m/d/Y", strtotime($find['work_date'])) . '">Edit it &raquo;</a>';
					}
				}
				foreach ( $this->view->errors as $err ) {
					if ( $err ) {
						$this->view->error_msg = 'One or more line entries are incomplete or have have an error. Please correct the highlighted entries and re-submit.';
						break;
					}
				}
			}
			else {
				$this->view->error_msg = 'Please enter at least one time entry.';
			}
			if ( $this->view->error_msg == '' ) {
				// save and show confirm
				foreach ( $entries as $k => $entry ) {
					$time = new Project_time();
					$time->loadArray($entry);
					$time->save();
				}
				$note = $this->view->clean['note'];
				Project_note::setNote($this->view->project->ID, $this->view->clean['date'], $note);
				$this->view->entries = $entries;
				echo $this->render('/managers/add_success');
				exit;
			}
		}
		echo $this->render('/managers/add');
	}

	function review() {
		$this->view->project = new Project($this->request->getPiece(2));
		$this->view->date = strtotime($_GET['date']);
		$this->view->entries = Project_time::getEntries(array(
			'project_id' => $this->view->project->ID,
			'work_date' => $_GET['date']
		));
		
		echo $this->render('/managers/review');
	}
	
}