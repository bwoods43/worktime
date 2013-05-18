<?php
class Admin_users_Controller extends Authrequired_Controller {

	public $auth_type_required = 'admin';
	
	public function index() {
		// route
		if ( $this->request->getPiece(2) != '' ) {
			call_user_func(array($this, $this->request->getPiece(2)));
			exit;
		}
		// default
		$limit = 20;
		$start = intval($_GET['start']);
		$this->view->users = User::getUsers( array(
			'exclude' => User::getCurrentUser()->ID,
			'start' => $start * $limit,
			'limit' => $limit
		));
		$total_users = User::getTotalUsers( true );
		$this->view->total_pages = ceil($total_users / $limit);
		$this->view->current_page = intval($_GET['start']) + 1;
		if ( $_SESSION['flash'] ) {
			$this->view->success = $_SESSION['flash'];
			$_SESSION['flash'] = '';
			
		}
		echo $this->render('/admin/users/index');
	}
	
	private function validate_clean() {
		if ( empty($this->view->clean['user']['first_name']) ) {
			$this->view->errors[] = 'Please enter a first name.';
		}
		if ( empty($this->view->clean['user']['last_name']) ) {
			$this->view->errors[] = 'Please enter a last name.';
		}
		// 7/3/10 baw - add foreman to same functionality as employees
		// employees and foreman = no login
		if ( $this->view->clean['user']['account_type'] != 'employee' && $this->view->clean['user']['account_type'] != 'foreman' ) {
			if ( !UB_Text::isEmail($this->view->clean['user']['email']) ) {
				$this->view->errors[] = 'Enter a valid email address for the user.';
			}
			elseif ( $this->user->emailExists($user->email) ) {
				$this->view->errors[] = 'This email address is in use by another account.';
			}
		}
	}
	
	public function hide() {
		$user = new User($this->request->getPiece(3));
		$user->hidden = 1;
		$user->save();
		$_SESSION['flash'] = $user->first_name . ' ' . $user->last_name . ' has been hidden from employee the listings and drop-downs.';
		$this->request->redirect('/admin/users', UB_Request::HTTP_303);
		exit;
	}
	
	public function unhide() {
		$user = new User($this->request->getPiece(3));
		$user->hidden = 0;
		$user->save();
		$_SESSION['flash'] = $user->first_name . ' ' . $user->last_name . ' has been un-hidden from employee the listings and drop-downs.';
		$this->request->redirect('/admin/users', UB_Request::HTTP_303);
		exit;
	}
	
	public function add() {
		$this->view->clean = array();
		if ( sizeof($_POST) ) {
			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			$this->view->errors = array();
			$this->user = new User();
			if ( $this->view->clean['user']['hidden'] != 1 ) {
				$this->view->clean['user']['hidden'] = 0;
			}
			$this->user->loadArray($this->view->clean['user']);
			$this->validate_clean();
			
			// 7/3/10 baw - add foreman to same functionality as employees
			// employees and foreman = no login
			if ( $this->view->clean['user']['account_type'] != 'employee' && $this->view->clean['user']['account_type'] != 'foreman' ) {
				if ( empty($this->view->clean['passwd']['passwd']) || empty($this->view->clean['passwd']['confirm']) ) {
					$this->view->errors[] = 'Enter and confirm a password for this new user.';
				}
				elseif ( $this->view->clean['passwd']['passwd'] !== $this->view->clean['passwd']['confirm'] ) {
					$this->view->errors[] = 'Password did not match confirmation.';
				}
			}
			
			if ( count($this->view->errors) <= 0 ) {
				// 2/26/11 baw - add office id
				$this->user->office_id = $_SESSION['beynon_office_id'];
				$this->user->passwd = User::encryptPassword($this->view->clean['passwd']['passwd']);
				$this->user->save();
				$this->view->success = 'This user has been created. <a href="/admin/users/edit/' . $this->user->ID . '">Edit them</a> or add another user below.';	
			// 7/3/10 baw - add foreman to same functionality as employees
				if ( $this->view->clean['notify'] && $this->view->clean['user']['account_type'] != 'employee' && $this->view->clean['user']['account_type'] != 'foreman' ) {
					$this->user->notify($this->view->clean['passwd']['passwd']);
				}
				$this->view->clean = array();	
			}
		}
		echo $this->render('/admin/users/add');
	}
	
	public function edit() {
		$this->user = new User($this->request->getPiece(3));
		$this->view->user = $this->user;
		$this->view->clean = array('user' => $this->user->getArray());
		
		if ( sizeof($_POST) ) {
			$this->view->clean = UB_Text::stringFromPost(array_keys($_POST));
			$this->view->errors = array();
			if ( $this->view->clean['user']['hidden'] != 1 ) {
				$this->view->clean['user']['hidden'] = 0;
			}
			$this->user->loadArray($this->view->clean['user']);
			$this->validate_clean();
			$passwd = $this->user->passwd;
			if ( !empty($this->view->clean['passwd']['passwd']) && empty($this->view->clean['passwd']['confirm']) || empty($this->view->clean['passwd']['passwd']) && !empty($this->view->clean['passwd']['confirm']) ) {
				$this->view->errors[] = 'Enter and confirm a password for this new user.';
			}
			elseif ( $this->view->clean['passwd']['passwd'] !== $this->view->clean['passwd']['confirm'] ) {
					$this->view->errors[] = 'Password did not match confirmation.';
			}
			else {
				$passwd = User::encryptPassword($this->view->clean['passwd']['passwd']);
			}
			if ( count($this->view->errors) <= 0 ) {
				$this->user->passwd = $passwd;
				$this->user->save();
				$this->view->success = 'Your changes have been saved.';	
			}
		}
		echo $this->render('/admin/users/edit');
	}
	
	public function delete() {
		$this->view->user = new User($this->request->getPiece(3));
		if ( sizeof($_POST) ) {
			$clean = UB_Text::stringFromPost(array_keys($_POST));
			if ( $clean['delete']['ID'] == $this->view->user->ID ) {
				$this->view->user->delete();
				$this->view->success = $this->view->user->first_name . ' ' . $this->view->user->last_name . "'s account has been deleted.";
				$this->view->success .= ' <a href="/admin/users">Back to manage users</a>';
			}
		}
		echo $this->render('/admin/users/delete');
	}
		
}