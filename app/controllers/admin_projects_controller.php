<?php
class Admin_projects_Controller extends Authrequired_Controller {

	public $auth_type_required = 'admin';
	
	public function index() {
		// route
		if ( $this->request->getPiece(2) != '' ) {
			call_user_func(array($this, $this->request->getPiece(2)));
			exit;
		}
		// default
		$this->view->projects = Project::getProjects();
		echo $this->render('/admin/projects/index');
	}

	public function getManager() {
		return new User($this->manager_id);
	}
	
	private function validate_clean() {
		$project = $this->view->clean['project'];
		if ( empty($project['project_name']) ) {
			$this->view->errors[] = 'Please enter a project name.';
		}
		$mgr = new User($project['manager_id']); 
		if ( !$mgr->ID ) {
			$this->view->errors[] = 'Please select a manager to enter time records for this project.';
		}
		if ( empty($project['start_date']) ) {
			$this->view->errors[] = 'Please enter a start date for this project.';
		}
		if ( empty($project['budget_end_date']) ) {
			$this->view->errors[] = 'Please enter an estimated end date for this project.';
		}
		if ( $project['budget_hours'] <= 0 ) { 
			$this->view->errors[] = 'Please enter the budgeted hours for this project.';
		}
		if ( count($project['tasks']) <= 0 ) {
			$this->view->errors[] = 'Please select at least one task for this project.';
		}
		// don't require employees to save a project.
//		if ( count($project['employees']) <= 0 ) {
//			$this->view->errors[] = 'Please select at least one employee to work on this project.';
//		}
	}
	
	public function add() {
		if ( sizeof($_POST) ) {
			$this->view->errors = array();
			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			$this->validate_clean();
			if ( count($this->view->errors) <= 0 ) {
				$project = new Project();
				$project->loadArray($this->view->clean['project']);
				$project->start_date = date("Y-m-d", strtotime($project->start_date));
				$project->budget_end_date = date("Y-m-d", strtotime($project->budget_end_date));
				$project->created = date("Y-m-d H:i:s");
				// 2/26/11 baw - add office id
				$project->office_id = $_SESSION['beynon_office_id'];
				$project->save();
				$project->setEmployees( $_POST['project']['employees'] );
				$project->setTasks( $this->view->clean['project']['tasks']);
				$this->view->success = 'Your new project was saved. <a href="/admin/projects/edit/' . $project->ID . '">Edit it</a> or add another below.';
				$this->view->clean = array();
			}
		}
		
		$this->view->tasks = Task::getTasks();
		$this->view->employees = User::getUsers(array('account_type'=>User::TYPE_EMPLOYEE_FOREMAN, 'limit' => 99999, 'hidden' => '0')); 
		$this->view->managers = User::getUsers(array('account_type'=>User::TYPE_MANAGER, 'limit' => 999999, 'hidden' => '0')); 
		echo $this->render('/admin/projects/add');
	}
	
	public function edit() {
		$project = new Project($this->request->getPiece(3));
		
		$this->view->clean = array( 'project' => $project->getArray() );
		$this->view->clean['project']['employees'] = $project->getEmployees();
		$this->view->clean['project']['tasks'] = $project->getTasks();
		
		if ( sizeof($_POST) ) {
			$this->view->errors = array();
			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			$this->validate_clean();
			if ( count($this->view->errors) <= 0 ) {
				$project->loadArray($this->view->clean['project']);
				$project->start_date = date("Y-m-d", strtotime($project->start_date));
				$project->budget_end_date = date("Y-m-d", strtotime($project->budget_end_date));
				$project->save();
				$project->setEmployees( $_POST['project']['employees'] );
				$project->setTasks( $this->view->clean['project']['tasks']);
				$this->view->success = 'Your changes have been saved.';
			}
		}
		
		$this->view->tasks = Task::getTasks();
		$this->view->employees = User::getUsers(array('account_type'=>User::TYPE_EMPLOYEE_FOREMAN, 'limit' => 99999, 'hidden' => '0')); 
		$this->view->managers = User::getUsers(array('account_type'=>User::TYPE_MANAGER, 'limit' => 99999, 'hidden' => '0')); 
		echo $this->render('/admin/projects/edit');
	}

	public function delete() {
		$this->view->project = new Project($this->request->getPiece(3));
		if ( sizeof($_POST) ) {
			if ( $_POST['delete']['ID'] == $this->view->project->ID ) {
				$this->view->project->delete();
				$this->view->success = '<q>' . $this->view->project->project_name . '</q> project and all related data has been deleted.';
			}
		}
		echo $this->render('/admin/projects/delete');
	}

	public function archive() {
		$this->view->project = new Project($this->request->getPiece(3));
		if ( sizeof($_POST) ) {
			if ( $_POST['archive']['ID'] == $this->view->project->ID ) {
				$db = UB_Db::getInstance();
				$sql = "
					UPDATE projects SET archived = 1 WHERE ID = " . $this->view->project->ID . " LIMIT 1";
				$db->query( $sql );
				$this->view->success = '<q>' . $this->view->project->project_name . '</q> project and all related data has been archived.';
			}
		}
		echo $this->render('/admin/projects/archive');
	}

	public function unarchive() {
		$this->view->project = new Project($this->request->getPiece(3));
		if ( sizeof($_POST) ) {
			if ( $_POST['unarchive']['ID'] == $this->view->project->ID ) {
				$db = UB_Db::getInstance();
				$sql = "
					UPDATE projects SET archived = 0 WHERE ID = " . $this->view->project->ID . " LIMIT 1";
				$db->query( $sql );
				$this->view->success = '<q>' . $this->view->project->project_name . '</q> project and all related data has been unarchived.';
			}
		}
		echo $this->render('/admin/projects/unarchive');
	}
	
}