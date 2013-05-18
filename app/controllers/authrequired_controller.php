<?php
class Authrequired_Controller extends UB_Controller {
	
	public $auth_type_required;
	
	public function __construct() {
		$this->request = UB_Request::getInstance();
		if ( is_null($this->auth_type_required) ) {
			if ( !User::isLoggedIn() ) {
				$this->request->redirect('/login');
				exit;
			}
		}
		else {
			if ( !User::isLoggedIn() || (User::getAccountType() != $this->auth_type_required && User::getAccountType() != User::TYPE_ADMIN) ) {
				$this->request->redirect('/login');
				exit;
			}
		}
		$this->view = new UB_View();
		$this->view->setPath(VIEW_PATH);
		$this->view->template_path = TEMPLATE_PATH . UB_DS . $this->template;
	}
	
}