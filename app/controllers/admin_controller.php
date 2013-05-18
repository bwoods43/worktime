<?php
class Admin_Controller extends AuthRequired_Controller {
	
	public function index() {
		echo $this->render('/admin/index');
	}

	
}