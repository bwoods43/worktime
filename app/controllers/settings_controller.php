<?php
class Settings_Controller extends Authrequired_Controller {
	
	public function index() {
		$this->view->clean = array( 'user' => User::getCurrentUser()->getArray() );
		if ( sizeof($_POST) ) {
			$clean = UB_Text::stringFromPost(array_keys($_POST));
			$user = $clean['user'];
			$pass = $clean['passwd'];
			$this->view->errors = array();
			if ( empty($user['first_name']) ) { 
				$this->view->errors[] = 'Enter your first name';
			}
			if ( empty($user['last_name']) ) { 
				$this->view->errors[] = 'Enter your last name';
			}
			if ( !UB_Text::isEmail($user['email']) ) { 
				$this->view->errors[] = 'Enter a valid email address';
			}
			elseif ( User::getCurrentUser()->emailExists($user['email']) ) {
				$this->view->errors[] = 'This email address is already in use by another user';
			}
			if ( !empty($pass['current']) || !empty($pass['new']) ) {
				if ( User::encryptPassword($pass['current']) != User::getCurrentUser()->passwd ) {
					$this->view->errors[] = 'Current password was invalid';
				}
				if ( empty($pass['new']) ) {
					$this->view->errors[] = 'Please enter a new password';
				}
				if ( count($this->view->errors) <= 0 ) {
					$user = User::getCurrentUser();
					$user->passwd = User::encryptPassword($pass['new']);
					$user->save();
				}
			}
			if ( count($this->view->errors) <= 0 ) {
				$cur = User::getCurrentUser();
				$cur->loadArray($user);
				$cur->save();
				$this->view->success = 'Your changes have been saved.';
			}
			$this->view->clean = $clean;
		}
		echo $this->render('/settings/index');
	}
	
}