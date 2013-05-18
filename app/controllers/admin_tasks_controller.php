<?php
class Admin_tasks_Controller extends Authrequired_Controller {

	public $auth_type_required = 'admin';
	
	public function index() {
		// route
		if ( $this->request->getPiece(2) != '' ) {
			call_user_func(array($this, $this->request->getPiece(2)));
			exit;
		}
		$this->view->tasks = Task::getTasks();
		echo $this->render('/admin/tasks/index');
	}

	public function add() {
		if ( sizeof($_POST) ) {
			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			$this->view->errors = array();
			if ( $this->view->clean['task']['task_name'] == '' ) {
				$this->view->errors[] = 'Enter a task name.';
			}
			else {
				$task = new Task();
				$task->find(array('task_name' => $this->view->clean['task']['task_name']));
				if ( $task->ID ) {
					$this->view->errors[] = 'This task name already exists. Try another name.';
				}
			}
			if ( count($this->view->errors) <= 0 ) {
				$task = new Task();
				$task->loadArray($this->view->clean['task']);
				// 02/02/13 baw tasks added by office
				$task->office_id = $_SESSION['beynon_office_id'];
				$task->save();
				$this->view->clean = array();
				$this->view->success = 'Task named <q>' . $task->task_name . '</q> was added successfully. <a href="/admin/tasks">Manage tasks</a> or add another below.';
			}
		}
		echo $this->render('/admin/tasks/add');
	}
	
	public function edit() {
		$this->view->task = new Task($this->request->getPiece(3));
		$this->view->clean['task'] = $this->view->task->getArray(); 
		
		if ( sizeof($_POST) ) {
			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			$this->view->errors = array();
			if ( $this->view->clean['task']['task_name'] == '' ) {
				$this->view->errors[] = 'Enter a task name.';
			}
			else {
				$task = new Task();
				$task->find(array('task_name' => $this->view->clean['task']['task_name']));
				if ( $task->ID != $this->task->ID ) {
					$this->view->errors[] = 'This task name already exists. Try another name.';
				}
			}
			if ( count($this->view->errors) <= 0 ) {
				$this->view->task->loadArray($this->view->clean['task']);
				$this->view->task->save();
				$this->view->success = 'Your changes have been saved.';
			}
		}
		echo $this->render('/admin/tasks/edit');
	}

// 02/09/13 baw removing task deletion functionality
/*
	function delete() {
		$this->view->task = new Task($this->request->getPiece(3));
		if ( sizeof($_POST)) {
			$clean = UB_Text::stringFromPost('delete');
			if ( $clean['ID'] == $this->view->task->ID ) {
				$this->view->task->delete();
				$this->view->success = 'The <q>' . $this->view->task->task_name . '</q> task was successfully deleted.';
			}
		}
		echo $this->view->render('/admin/tasks/delete');
	}
*/	
}